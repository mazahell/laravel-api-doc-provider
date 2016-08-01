<?php

namespace RestioDocProvider\Console;

use App\Models\Docs;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use RestioDocProvider\Controllers\DocController;
use zpt\anno\Annotations;

class GenerateDocsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API Docs from annotations in Controllers';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param DocController $docController
     * @return mixed
     */
    public function handle(DocController $docController)
    {
        // $type_request = '';
        $file_name = 'postman-collection.json';
        $inserted  = 0;
        Docs::truncate();

        // Global search all Controllers
        $ctrlds = glob(app_path("Http/Controllers") . '/*/*.php');
        $ctrls  = glob(app_path("Http/Controllers") . '/*.php');

        // Prepare finded controllers
        $controllers = array_merge($ctrlds, $ctrls);
        array_walk($controllers, function (&$val) {
            $path     = str_replace("/", "\\", $val);
            $segments = explode("\\", $path);
            $key      = array_search("Controllers", $segments);
            $val      = str_replace(".php", "", 'App\\Http\\Controllers\\' . implode('\\', array_slice($segments, $key + 1)));
        });

        // User Reflaction Class for parse annotations
        foreach ($controllers as $c) {

            $classReflector = new ReflectionClass(new $c);

            $methodAnnotations = [];
            foreach ($classReflector->getMethods() as $methodReflector) {
                $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
            }

            // get All Routes for buiding REST API
            $routeNames = [];
            foreach (Route::getRoutes() as $value) {
                if ($value->getActionName() != 'Closure') {
                    preg_match("~(([\w]+)@)~i", $value->getActionName(), $m);
                    $methods = $value->getMethods();
                    $keyHead = array_search("HEAD", $methods);
                    if (is_int($keyHead)) {
                        unset($methods[$keyHead]);
                    }

                    $routeNames[$value->getName()] = [
                        'methods'    => implode(" | ", $methods),
                        'url'        => "/" . $value->getPath(),
                        'controller' => preg_replace("~Controller~", "", $m[2])
                    ];

                }
            }
            
            foreach ($methodAnnotations as $annotation) {

                if (isset($annotation['description'])
                    && isset($annotation['route'])
                    && isset($annotation['required_params'])
                    && isset($annotation['optional_params'])
                ) {

                    $req_params = is_array($annotation['required_params']) ? $this->prepareParams($annotation['required_params']) : [];
                    $opt_params = is_array($annotation['optional_params']) ? $this->prepareParams($annotation['optional_params']) : [];

                    $success_response = file_exists(storage_path("api_docs/") . $annotation['route'] . '.json') ? file_get_contents(storage_path("api_docs/") . $annotation['route'] . '.json') : "__null__";

                    $docs                   = new Docs();
                    $docs->controller       = $routeNames[$annotation['route']]['controller'];
                    $docs->method           = $routeNames[$annotation['route']]['methods'];
                    $docs->route            = $routeNames[$annotation['route']]['url'];
                    $docs->description      = $annotation['description'];
                    $docs->required_params  = json_encode($req_params);
                    $docs->optional_params  = json_encode($opt_params);
                    $docs->success_response = $success_response;
                    $docs->save();
                    $inserted++;

                }
            }
        }
        $this->comment("Updated routes: " . $inserted);

        // Export variables to postman collection
        $this->comment("Start export to: " . $file_name);

        // Export
        $json = $docController->exportPostman('cli');
        try {
            file_put_contents(base_path($file_name), $json);
        } catch (Exception $e) {
            $this->comment("Exception when creating export file: " . $file_name);
        }
        $this->comment("postman-collection.json successfully updated!");

    }

    /**
     * Prepare params for json Encode
     *
     * @param array $params
     * @return array
     */
    private function prepareParams(array $params)
    {
        $preedited = [];
        if (count($params) > 0) {
            foreach ($params as $field) {
                $fields = Docs::$fields[$field];
                unset($fields['field']);
                $preedited[Docs::$fields[$field]['field']] = $fields;
            }
        }

        return $preedited;
    }
}

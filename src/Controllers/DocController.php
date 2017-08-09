<?php

namespace RestioDocProvider\Controllers;

use App\Models\Docs;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class DocController extends Controller
{
    /**
     * Show main page with documentation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function docs()
    {
        return view("restio_doc::index");
    }


    /**
     * Get main JSON response for Build Api DOC
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiJSON()
    {
        $docs_group = Docs::groupBy("docs.controller")
            ->selectRaw("(select count(*) from docs as d where d.controller = docs.controller) as count, controller")
            ->orderBy("docs.controller")
            ->get(['docs.controller'])
            ->toArray();
        $controllers = array_column($docs_group, 'controller');
        $all_docs    = Docs::get(['controller', 'method', 'route as name', 'description', 'required_params', 'optional_params', 'success_response as response'])->toArray();

        usort(
            $all_docs,
            function ($a, $b) {
                return strnatcmp($a['name'], $b['name']);
            }
        );

        $parts = [];
        foreach ($controllers as $key => $part) {
            $parts[$key]['name'] = $part;

            foreach ($all_docs as $doc) {
                if ($doc['controller'] == $part) {
                    $doc['required_params'] = json_decode($doc['required_params'], 1);
                    $doc['optional_params'] = json_decode($doc['optional_params'], 1);
                    $doc['response']        = json_decode($doc['response'], 1);
                    $parts[$key]['data'][]  = $doc;
                }
            }
        }

        $response = [
            'apiName' => config("restio_doc.api_name", "Your APINAME"),
            'parts'   => $parts,
        ];

        return response()->json($response);
    }

    /**
     * Export postman-collection.json
     *
     * @param string $type
     * @return \Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     */
    public function exportPostman($type = 'web')
    {
        $collection_id = str_random(36);
        $requests      = [];
        $req           = Docs::orderBy("route")->get()->toArray();

        foreach ($req as $key => $request) {

            $start = 0;

            $params = array_merge(json_decode($request['required_params'], 1), json_decode($request['optional_params'], 1));

            $requests[$key]['collectionId'] = $collection_id;
            $requests[$key]['id']           = str_random(36);
            $requests[$key]['name']         = $request['route'];
            $requests[$key]['description']  = $request['description'];
            $requests[$key]['url']          = url($request['route']);

            $requests[$key]['method']  = $request['method'];
            $requests[$key]['headers'] = "";

            $url = [];
            foreach ($params as $k => $p) {
                $requests[$key]['data'][$start]['key']   = $k;
                $requests[$key]['data'][$start]['value'] = isset($p['default']) ? $p['default'] : "";
                $requests[$key]['data'][$start]['type']  = isset($p['type']) && strtolower($p['type']) == 'file' ? "file" : 'text';
                $start++;
            }

            if ($request['method'] == "GET") {
                foreach ($params as $kk => $par) {
                    if (!empty($par['default'])) {
                        $url[$kk] = $par['default'];
                    }
                }
            }

            if ($url) {
                $requests[$key]['url'] = $requests[$key]['url'] . '?' . http_build_query($url);
            }

            $requests[$key]['dataMode']  = "params";
            $requests[$key]['timestamp'] = 0;
            $requests[$key]['version']   = 2;
            $requests[$key]['time']      = time();
        }
        $postman = [
            'id'        => $collection_id,
            'name'      => config("restio_doc.api_name", "Your APINAME"),
            'timestamp' => time(),
            'requests'  => $requests,
        ];

        // Response from CLI interafce (for write postman collection)
        if ($type == 'cli') {
            return json_encode($postman);
        }

        return response(json_encode($postman), 200, ['Content-disposition' => 'attachment; filename=postman-collection.json', 'Content-type' => 'application/json']);
    }

    public function laravelUrls()
    {
        $req = Docs::get(['id', 'controller', 'method', 'route'])->toArray();
        $gr  = [];
        foreach ($req as $r) {
            $route_prefix = explode("/", $r['route']);
            $gr[]         = $route_prefix[1];
        }
        $uroutes = array_flip(array_unique($gr));
        array_walk($uroutes, function (&$val) {
            $val = [];
        });

        foreach ($req as $key => $r) {
            $arr_sergments = array_values(array_filter(explode("/", $r['route'])));
            $route_key     = $arr_sergments[0];
            if (array_key_exists($route_key, $uroutes)) {
                if ($arr_sergments[0] == $route_key) {
                    $arr_sergments[0] = "_";
                }
                if (count($arr_sergments) >= 2) {
                    unset($arr_sergments[0]);
                }
                $uroutes[$route_key][implode("_", $arr_sergments)] = url($r['route']);
            }
        }

        $angular_urls = "var API_URLS = " . json_encode($uroutes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ";" . PHP_EOL;

        return response($angular_urls, 200, ['Content-disposition' => 'attachment; filename=laravel_urls.js', 'Content-type' => 'text/javascript']);
    }

    public function generate_docs()
    {
        $param = null;
        try {
            Artisan::call("generate:docs");
            $param = "true";
        } catch (\Exception $e) {
            $param = "false";
        }

        return redirect(route("restio_docs") . "?" . $param);
    }
}

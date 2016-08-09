<?php

namespace RestioDocProvider;

use Illuminate\Support\ServiceProvider;

class RestioDocProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__ . '/routes.php';

        // For vendor publish artisan command
        $this->publishes([

            // Files
            __DIR__ . '/../res/app/Models/Docs.php'                        => app_path("Models/Docs.php"),
            __DIR__ . '/config.php'                                        => config_path("restio_doc.php"),
            __DIR__ . '/../res/tests/RestioExampleTest.php'                => base_path("tests/RestioExampleTest.php"),
            __DIR__ . '/../res/app/Http/Controllers/ExampleController.php' => app_path("Http/Controllers/ExampleController.php"),

            // Dirs
            __DIR__ . '/../res/public/api'                                 => base_path("public/restio_api"),
            __DIR__ . '/../res/database/migrations/docs.php'               => database_path("migrations/2016_07_01_000000_create_docs_table.php"),
            __DIR__ . '/../res/storage/api_docs'                           => storage_path("api_docs"),
        ]);

        // Routes
        $this->loadViewsFrom(__DIR__ . '/../res/resources/views', 'restio_doc');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands("RestioDocProvider\\Console\\GenerateDocsCommand");
    }
}

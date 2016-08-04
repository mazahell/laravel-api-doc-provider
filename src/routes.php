<?php
use Illuminate\Support\Facades\Route;

/**
 * Use prefix for hide real URL in custom projects
 */
Route::group(['prefix' => config("restio_doc.route_prefix", "")], function () {
    Route::get("docs", "RestioDocProvider\\Controllers\\DocController@docs");
    Route::get("docs/generate", "RestioDocProvider\\Controllers\\DocController@generate_docs")->name("generate_docs");
    Route::get('docs/postman.json', "RestioDocProvider\\Controllers\\DocController@exportPostman")->name("docs_postman");
    Route::get('docs/api.json', 'RestioDocProvider\\Controllers\\DocController@apiJSON')->name("docs_json");
    Route::get('docs/laravel_urls.js', 'RestioDocProvider\\Controllers\\DocController@laravelUrls')->name("restio_angular_url");
});

if (config("restio_doc.usage_default_routes")) {
    Route::get("example_route", "App\\Http\\Controllers\\ExampleController@index")->name("example_index");
}
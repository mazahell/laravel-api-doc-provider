<?php

class RestioExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
            ->see('Laravel 5');
    }

    public function testExample()
    {
        $res = $this->json("GET", route("example_index"), ['token' => 'token_1', 'page' => 2]);

        $res->assertStatus(200);
        $this->writeSuccessResponse('example_index', $res);
    }

    /**
     * Autogenerator Restio DOCS
     * Write success Response from API testings
     *
     * @param string $route_name
     * @param $response \Illuminate\Http\Response
     * @internal param $success_response
     * TODO::move this method to TestCase for usage in other tests!!!
     */
    protected function writeSuccessResponse($route_name, $res)
    {
        $response_body = $res instanceof \Illuminate\Http\Response ? $res->baseResponse->content() : '';
        if (strlen($route_name) > 0 && strlen($response_body) > 0) {
            @file_put_contents(storage_path('api_docs/' . $route_name . '.json'), $response_body);
        }
    }

}

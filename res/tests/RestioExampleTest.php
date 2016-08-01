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
        $this->json("GET", route("example_index"), ['token' => 'token_1', 'page' => 2]);

        $this->assertResponseOk();
        $this->writeSuccessResponse('example_index');
    }

    /**
     * Autogenerator Restio DOCS
     * Write success Response from API testings
     *
     * @param $route_name
     * @param $success_response
     * TODO::move this method to TestCase for usage in other tests!!!
     */
    protected function writeSuccessResponse($route_name = '', $success_response = "")
    {
        $success_response = $this->response->getContent() ? $this->response->getContent() : $success_response;
        if (strlen($route_name) > 0 && strlen($success_response) > 0) {
            @file_put_contents(storage_path('api_docs/' . $route_name . '.json'), $success_response);
        }
    }

}

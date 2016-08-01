<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @route                example_index
     * @description          Example description for route
     * @required_params      [token]
     * @optional_params      [page]
     */
    public function index(Request $request)
    {
        $token = $request->input("token");
        $page  = $request->input("page", 1);

        return response()->json([
            'error' => false,
            'token' => $token,
            'page'  => $page,
        ]);
    }
}

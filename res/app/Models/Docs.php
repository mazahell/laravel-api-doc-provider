<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docs extends Model
{
    public $timestamps = false;
    public static $fields = [
        'token' => [
            'description' => 'User token, random string',
            'type'        => 'String',
            'field'       => 'token',
            'default'     => 'token_str'
        ],
        'page'  => [
            'description' => 'Number of page',
            'type'        => 'Integer',
            'field'       => 'page',
            'default'     => 1
        ]
    ];

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string("controller", 50)->comment = "Main Controller URL";
            $table->string("method", 50)->comment     = "GET, POST, etc.";
            $table->string("route", 50)->comment      = "Route API";
            $table->string("description")->comment    = "Description for Route";
            $table->text("required_params")->comment  = "Required array params";
            $table->text("optional_params")->comment  = "Optional array params";
            $table->text("success_response")->comment = "Json success response";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('docs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('address');
            $table->longText('description')->nullable();
            $table->longText('features')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_featured')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}

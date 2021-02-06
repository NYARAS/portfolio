<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectProjectTypePivotTable extends Migration
{
    public function up()
    {
        Schema::create('project_project_type', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id', 'project_id_fk_3134490')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('project_type_id');
            $table->foreign('project_type_id', 'project_type_id_fk_3134490')->references('id')->on('project_types')->onDelete('cascade');
        });
    }
}

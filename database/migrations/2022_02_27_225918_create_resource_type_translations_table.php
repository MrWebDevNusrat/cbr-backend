<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_type_translations', function (Blueprint $table) {
            $table->unsignedInteger('resource_type_id');
            $table->string('language');
            $table->string('name');
            $table->timestamps();
            $table->primary(['resource_type_id', 'language']);
            $table->foreign('language')->references('code')->on('languages');
            $table->foreign('resource_type_id')->references('id')->on('resource_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_type_translations');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbrs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('valute_id')->nullable();
            $table->integer('num_code')->nullable();
            $table->string('char_code')->nullable();
            $table->string('nominal')->nullable();
            $table->string('value')->nullable();
            $table->unsignedInteger('date_id');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();

            $table->foreign('date_id')->references('id')->on('dates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cbrs');
    }
}

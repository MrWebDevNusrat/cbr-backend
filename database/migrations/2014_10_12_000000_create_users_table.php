<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('firstname')->nullable();
            $table->string('surname')->nullable();
            $table->string('patronymic')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(1);
            $table->string('phone', 20)->nullable();
            $table->enum('role', ['user', 'librarian', 'admin', 'moderator']);
            $table->integer('created_by')->nullable();
            $table->integer('permission_group_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->string('verify_code')->nullable();
            $table->string('profile_img')->nullable();
            $table->timestamp('verify_code_expire')->nullable();
            $table->string('show_password')->nullable();
            $table->string('device_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

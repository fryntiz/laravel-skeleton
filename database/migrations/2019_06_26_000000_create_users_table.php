<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on('users_roles')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->unsignedBigInteger('detail_id');
            $table->foreign('detail_id')
                ->references('id')->on('users_detail')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->unsignedBigInteger('data_id');
            $table->foreign('data_id')
                ->references('id')->on('users_data')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->unsignedBigInteger('configuration_id');
            $table->foreign('configuration_id')
                ->references('id')->on('users_configuration')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->string('name');
            $table->string('nick')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken()->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['detail_id']);
            $table->dropForeign(['data_id']);
            $table->dropForeign(['configuration_id']);
        });
    }
}

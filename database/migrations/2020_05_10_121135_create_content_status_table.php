<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_status', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('file_id')
                ->nullable()
                ->comment('FK a la imagen en la tabla files');
            $table->foreign('file_id')
                ->references('id')->on('files')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->string('name', 511)
                ->unique()
                ->comment('Nombre de la categoría');
            $table->string('slug', 255)
                ->unique()
                ->comment('Slug para el URL');
            $table->string('description', 1023)
                ->nullable()
                ->comment('Descripción acerca de lo que contendrá esta categoría');
            $table->string('icon', 255)->nullable()->comment('Clase css para el icono');
            $table->string('color', 255)
                ->default('#000000')
                ->comment('Código Hexadecimal del color');
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
        Schema::dropIfExists('content_status', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
        });
    }
}

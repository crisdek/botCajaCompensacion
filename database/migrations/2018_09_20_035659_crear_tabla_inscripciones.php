<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaInscripciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('persona_id');
            $table->unsignedInteger('actividad_id');
            $table->timestamps();

            // Llave foranea hacía la tabla personas
            $table->foreign('persona_id')
            ->references('id')->on('personas')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            // Llave foranea hacía la tabla actividades
            $table->foreign('actividad_id')
            ->references('id')->on('actividades')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripciones');
    }
}

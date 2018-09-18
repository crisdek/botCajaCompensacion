<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaSolicitudesIngreso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_ingreso', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('persona_id');
            $table->unsignedInteger('grupo_interes_id');
            $table->char('estado', 1);
            $table->timestamps();

            // Llave foranea hacía la tabla personas
            $table->foreign('persona_id')
            ->references('id')->on('personas')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            // Llave foranea hacía la tabla grupos_interes
            $table->foreign('grupo_interes_id')
            ->references('id')->on('grupos_interes')
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
        Schema::dropIfExists('solicitudes_ingreso');
    }
}

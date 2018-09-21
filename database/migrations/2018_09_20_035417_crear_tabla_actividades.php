<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaActividades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('grupo_interes_id');
            $table->string('nombre', 64);
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->unsignedInteger('duracion');
            $table->unsignedInteger('costo');
            $table->char('estado', 1);
            $table->timestamps();

            // Llave foranea hacía la grupos de interes
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
        Schema::dropIfExists('actividades');
    }
}

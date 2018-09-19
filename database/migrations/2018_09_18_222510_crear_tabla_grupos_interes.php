<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaGruposInteres extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_interes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tema_id');
            $table->string('nombre', 128);
            $table->text('descripcion')->nullable();
            $table->timestamps();

            //$table->engine = 'InnoDB';

            // Llave foranea hacía la tabla temas
            $table->foreign('tema_id')
            ->references('id')->on('temas')
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
        Schema::dropIfExists('grupos_interes');
    }
}

<?php

use Illuminate\Database\Seeder;

class SolicitudIngresoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\SolicitudIngreso::create(['persona_id' => '1','grupo_interes_id'=>'4','estado'=>'0']);
	    App\SolicitudIngreso::create(['persona_id' => '2','grupo_interes_id'=>'5','estado'=>'0']);
	    App\SolicitudIngreso::create(['persona_id' => '3','grupo_interes_id'=>'6','estado'=>'0']);
    }
}
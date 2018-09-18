<?php

use Illuminate\Database\Seeder;

class GrupoInteresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Pastelería']);
	    App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Comida internacional']);
        App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Comida vegetariana']);
        App\GrupoInteres::create(['tema_id' => 2, 'nombre' => 'Fútbol']);
	    App\GrupoInteres::create(['tema_id' => 2, 'nombre' => 'Baloncesto']);
        App\GrupoInteres::create(['tema_id' => 3, 'nombre' => 'Word']);
        App\GrupoInteres::create(['tema_id' => 3, 'nombre' => 'Excel']);
    }
}

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
        App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Pastelería', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
	    App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Comida internacional', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
        App\GrupoInteres::create(['tema_id' => 1, 'nombre' => 'Comida vegetariana', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
        App\GrupoInteres::create(['tema_id' => 2, 'nombre' => 'Fútbol', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
	    App\GrupoInteres::create(['tema_id' => 2, 'nombre' => 'Baloncesto', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
        App\GrupoInteres::create(['tema_id' => 3, 'nombre' => 'Word', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
        App\GrupoInteres::create(['tema_id' => 3, 'nombre' => 'Excel', 'descripcion' => 'Este grupo tiene por objetivo vincular a las personas en torno a ...']);
    }
}

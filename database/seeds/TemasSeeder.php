<?php

use Illuminate\Database\Seeder;

class TemasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Tema::create(['nombre' => 'Culinaria']);
	    App\Tema::create(['nombre' => 'Deportes']);
	    App\Tema::create(['nombre' => 'Informática']);
    }
}

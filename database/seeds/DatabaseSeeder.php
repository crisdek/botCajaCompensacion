<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(TemasSeeder::class);
        $this->call(GrupoInteresSeeder::class);
        $this->call(ActividadesSeeder::class);
        $this->call(SolicitudIngresoSeeder::class);
    }
}

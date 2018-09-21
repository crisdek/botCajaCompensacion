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
<<<<<<< HEAD
        $this->call(SolicitudIngresoSeeder::class);
=======
>>>>>>> 2bf3fb2d82912be0052495d53a983d7ff6221134
    }
}

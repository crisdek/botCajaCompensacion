<?php

use Illuminate\Database\Seeder;

class ActividadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Actividad::create(['grupo_interes_id' => 1, 'nombre' => 'Introducción a la Pastelería', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-02', 'duracion' => '60', 'costo' => '50000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 1, 'nombre' => 'Ingredientes', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-03', 'duracion' => '60', 'costo' => '60000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 2, 'nombre' => 'Introducción a la Comida internacional', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-04', 'duracion' => '60', 'costo' => '50000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 2, 'nombre' => 'Ingredientes', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-05', 'duracion' => '60', 'costo' => '60000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 3, 'nombre' => 'Introducción a la Comida vegetariana', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-06', 'duracion' => '60', 'costo' => '50000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 3, 'nombre' => 'Ingredientes', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-07', 'duracion' => '60', 'costo' => '60000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 4, 'nombre' => 'Fútbol para niños', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-08', 'duracion' => '60', 'costo' => '50000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 4, 'nombre' => 'Fútbol para adolescentes', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-09', 'duracion' => '60', 'costo' => '60000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 5, 'nombre' => 'Baloncesto para niños', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-10', 'duracion' => '60', 'costo' => '50000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 5, 'nombre' => 'Baloncesto para adolescentes', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-11', 'duracion' => '60', 'costo' => '80000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 6, 'nombre' => 'Word básico', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-12', 'duracion' => '60', 'costo' => '90000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 6, 'nombre' => 'Word intermedio', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-13', 'duracion' => '60', 'costo' => '70000', 'estado' => 'P']);
        App\Actividad::create(['grupo_interes_id' => 7, 'nombre' => 'Excel avanzado', 'descripcion' => 'Está actividad pretende profundizar en ...', 'fecha' => '2018-10-14', 'duracion' => '60', 'costo' => '90000', 'estado' => 'P']);
    }
}

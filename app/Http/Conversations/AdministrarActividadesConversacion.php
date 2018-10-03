<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class AdministrarActividadesConversacion extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->mostrarMenuActividades();
    }
     /**
     * Muestra el menú para gestionar las actividades.
     *
     * 
     */
    
    public function mostrarMenuActividades()
    {
        // Preparar los botones de acuerdo con los servicios existentes
        $botones = array();
        $botones[] = Button::create('Consultar grupos de interés')->value(1);
        $botones[] = Button::create('Volver al menú de administración')->value(2);

        $cualOpcion = Question::create("Elige la operación que quieres realizar")->addButtons($botones);

        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                switch($this->opcion)
                {
                    case 1: 
                        $this->consultarGrupos();
                        break;
                    case 2: 
                        $this->bot->startConversation(new AdministrarSistemaConversacion());
                        break;
                   
                }
            }
            else
            {
                // Si el usuario digitó su respuesta como texto
                $this->say('Por favor elige una opción de la lista.');
                $this->repeat();
            }

        });
    }

    /**
     * Esta función permit consultar los grupos de interés
     */
    public function consultarGrupos()
    {
        // Se consultar todos los gr
        $grupos = \App\GrupoInteres::orderBy('tema_id', 'asc')->get();
        //$this->tema->where('nombre',$this->tema->nombre)
        // Mostrar los temas uno por uno
        $botones = array();
        foreach($grupos as $grupo)
        {
            $botones[] = Button::create($grupo->nombre)->value($grupo->id);
        }

        if(count($grupos) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, en este momento no hay grupos .");
        }
        else
        {
            $cualOpcion = Question::create("Los siguientes son los grupos, elige uno para ver luego sus actividades")->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->id = $answer->getValue();
                    $this->mostrarInformacionGrupo();
                }
                else
                {
                    // Si el usuario digitó su respuesta como texto
                    $this->bot->typesAndWaits(1);
                    $this->say('Por favor elige una opción de la lista.');
                    $this->repeat();
                }

            });
        }
    }


    /**
     * Muestra la información de un grupo y permite luego listar sus actividades
     */
    public function mostrarInformacionGrupo()
    {
        // Obtener la información del tema seleccionado
        $this->grupo = \App\GrupoInteres::find($this->id);

        $botones = array();
        $botones[] = Button::create('Ver actividades para cambiar su estado')->value(1);
        $botones[] = Button::create('Adicionar actividad al grupo')->value(2);
        $botones[] = Button::create('Nada')->value(3);

        $cualOpcion = Question::create("Selecciona la operación que quieres realizar sobre las actividades del grupo {$this->grupo->nombre}")->addButtons($botones);

        $this->bot->typesAndWaits(1);
        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                if($this->opcion == 1)
                {
                    $this->consultarActividades();

                  
                }
                else if ($this->opcion  == 2)
                {
                    $this->confirmarCreacionActividad();
                  
                    
                }
                else
                {
                    $this->bot->typesAndWaits(1);
                    $this->say('Bueno, tal vez en una próxima ocasión.');
                }
            }
            else
            {
                // Si el usuario digitó su respuesta como texto
                $this->bot->typesAndWaits(1);
                $this->say('Por favor elige una opción de la lista.');
                $this->repeat();
            }

        });


    }


     /**
     * Esta función permit consultar las actividades de acuerdo al grupo de interés seleccionado
     */
    public function consultarActividades()
    {
        // Se consultar todas las actividades
       
        $actividades = \App\Actividad::where('grupo_interes_id', $this->grupo->id)->where('estado', '<>', 'C')->get(); 
        
    
        
        // Mostrar los temas uno por uno
        $botones = array();
        foreach($actividades as $actividad)
        {
            $botones[] = Button::create($actividad->nombre)->value($actividad->id);
        }

        if(count($actividades) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, en este momento no hay actividades relacionadas con este grupo .");
        }
        else
        {
            $cualOpcion = Question::create("Los siguientes son las actividades del grupo de interés ".$this->grupo->nombre.'. Por favor selecciona una actividad para cambiar su estado ')->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->idactividad = $answer->getValue();
                    $this->mostrarInformacionActividad();
                }
                else
                {
                    // Si el usuario digitó su respuesta como texto
                    $this->bot->typesAndWaits(1);
                    $this->say('Por favor elige una opción de la lista.');
                    $this->repeat();
                }

            });
        }
    }



    /**
     * Muestra la información de una actividad para cambiar su estado
     */
    public function mostrarInformacionActividad()
    {
        // Obtener la información del tema seleccionado
        $this->actividad = \App\Actividad::find($this->idactividad );

        $botones = array();
        $botones[] = Button::create('Aplazar')->value(1);
        $botones[] = Button::create('Cancelar')->value(2);
        $botones[] = Button::create('Programar')->value(3);
        $botones[] = Button::create('Nada')->value(4);

        $cualOpcion = Question::create("Selecciona la operación que quieres realizar sobre la actividad {$this->actividad->nombre}")->addButtons($botones);

        $this->bot->typesAndWaits(1);
        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                if($this->opcion == 1)
                {
                        $this->ask("¿Cual es la nueva fecha para la actividad (dd/mm/aaaa)?", function (Answer $answer)
                        {
                            if($this->validarFecha($answer->getText()))
                            {
                           
                            
                            $valor_fecha= $answer->getText();//
                            $partes = explode ("/", $valor_fecha);
                            $g_cadena=$partes[2].'-'.$partes[1].'-'.$partes[0];
                            $this->actividad->fecha =  $g_cadena; 
                    
                            $this->actividad->estado = 'A';
                            
                            $this->actividad->save();

                            $this->bot->typesAndWaits(1);
                            $this->say('He aplazado la actividad.');
                            $this->bot->startConversation(new AdministrarSistemaConversacion());
                    
                                
                        }
                            else
                        {
                                $this->say("Esa fecha parece ser incorrecta, por favor verifícala.");
                                $this->repeat();
                        }
                    });
                }
                else if ($this->opcion  == 2)
                {
                    


                        $confirmacion = Question::create('¿Estás seguro de que deseas realmente cancelar esta actividad?'.$this->actividad->nombre)
                        ->addButtons([
                            Button::create('Confirmar')->value('si'),
                            Button::create('Cancelar')->value('no')
                        ]);
               
                    $this->ask($confirmacion, function (Answer $answer2) {
                        if ($answer2->isInteractiveMessageReply()) {
                            $opcion = $answer2->getValue();
                        
                            if($opcion == "si")
                            {
                                $this->say('Entendido, voy a cancelar la actividad '.$this->actividad->nombre);
                                
                                $this->say('Se canceló la actividad...');
                                
                                $this->actividad->estado = 'C';
                                
                                $this->actividad->save();
                                
                                $this->bot->startConversation(new AdministrarSistemaConversacion());


                            }
            
                            if($opcion == "no")
                            {
                                $this->say('Entendido, cancelaré la solicitud.');
                                $this->bot->startConversation(new AdministrarSistemaConversacion());
                            }
                        } else {
                            $this->say('Por favor elige una opción de la lista.');
                            $this->repeat();
                        }
                    });


                    
                

                    
                }
                else if ($this->opcion  == 3)
                {
                    


                        $confirmacion = Question::create('¿Estás seguro de que deseas realmente programar de nuevo esta actividad?'.$this->actividad->nombre)
                        ->addButtons([
                            Button::create('Confirmar')->value('si'),
                            Button::create('Cancelar')->value('no')
                        ]);
               
                    $this->ask($confirmacion, function (Answer $answer2) {
                        if ($answer2->isInteractiveMessageReply()) {
                            $opcion = $answer2->getValue();
                        
                            if($opcion == "si")
                            {
                                $this->say('Entendido, voy a programar la actividad '.$this->actividad->nombre);
                                
                                $this->say('Se programó la actividad...');
                                
                                $this->actividad->estado = 'P';
                                
                                $this->actividad->save();
                                
                                $this->bot->startConversation(new AdministrarSistemaConversacion());


                            }
            
                            if($opcion == "no")
                            {
                                $this->say('Entendido, cancelaré la solicitud.');
                                $this->bot->startConversation(new AdministrarSistemaConversacion());
                            }
                        } else {
                            $this->say('Por favor elige una opción de la lista.');
                            $this->repeat();
                        }
                    });


                    
                

                    
                }
                else
                {
                    $this->bot->typesAndWaits(1);
                    $this->say('Bueno, tal vez en una próxima ocasión.');
                }
            }
            else
            {
                // Si el usuario digitó su respuesta como texto
                $this->bot->typesAndWaits(1);
                $this->say('Por favor elige una opción de la lista.');
                $this->repeat();
            }

        });


    }
       /**
     *Esta función confirma la creación de una actividad 
     */
    public function confirmarCreacionActividad()
    {

        $this->ask("¿Cuál será el nombre de la nueva actividad?", function (Answer $answer)
        {   
                $this->nombre = $answer->getText();

                
                $this->preguntarDescripcion();
             
      
               

        });
        
        
    
  
    }
    public function preguntarDescripcion() {
        $this->ask("Escriba la descripción de la nueva actividad?", function (Answer $answer)
        {   
                $this->descripcion = $answer->getText();
                $this->preguntarFecha();
        }); 

        
    }
    public function preguntarFecha() {
      $this->ask("¿Cual es la fecha programada para la nueva actividad (dd/mm/aaaa)?", function (Answer $answer)
        {
            if($this->validarFecha($answer->getText()))
            {
           
            
            $valor_fecha= $answer->getText();//
            $partes = explode ("/", $valor_fecha);
            $g_cadena=$partes[2].'-'.$partes[1].'-'.$partes[0];
            $this->fecha =  $g_cadena; 
            $this->preguntarDuracion();
           
                
        }
            else
        {
                $this->say("Esa fecha parece ser incorrecta, por favor verifícala.");
                $this->repeat();
        }
      });
    }

    public function preguntarDuracion() {
             
        
        $this->ask("¿Cual es la duración para la nueva actividad (Ingrese sólo números)?", function (Answer $answer)
        {
            if($this->validarEntero($answer->getText()))
            {
           
            
                $this->duracion= $answer->getText();//
                $this->preguntarCosto();
           }
            else
              {
                      $this->say("La duración es incorrecta, debe ser un número, por favor verifícala.");
                      $this->repeat();
              }
        });

    }
    
   

     public function preguntarCosto() {
             
        
            $this->ask("¿Cual es el costo para la nueva actividad (Ingrese sólo números)?", function (Answer $answer)
            {
                if($this->validarEntero($answer->getText()))
                {
               
                
                    $this->costo= $answer->getText();//
                    $this->guardarActividad();
                    
               }
                else
                  {
                          $this->say("El costo es incorrecto, debe ser un número, por favor verifícalo.");
                          $this->repeat();
                  }
            });

        }

        public function guardarActividad() {
       
        

                $confirmacion = Question::create('¿Estás seguro de que deseas agregar esta actividad?'.$this->nombre)
            ->addButtons([
                Button::create('Confirmar')->value('si'),
                Button::create('Cancelar')->value('no')
            ]);

        $this->ask($confirmacion, function (Answer $answer2) {
            if ($answer2->isInteractiveMessageReply()) {
                $opcion = $answer2->getValue();
            
                if($opcion == "si")
                {
                    $this->say('Entendido, voy a agregar la actividad '.$this->nombre);
                    $this->crearActividad($this->id,$this->nombre,$this->descripcion,$this->fecha,$this->duracion,$this->costo);
                   
                }

                if($opcion == "no")
                {
                    $this->say('Entendido, cancelaré la solicitud.');
                    $this->mostrarMenuTemas();
                }
            } else {
                $this->say('Por favor elige una opción de la lista.');
                $this->repeat();
            }
        });
               
    
     
    }
    /**
     * Esta función permite crear las actividades
     */
    public function crearActividad($grupo,$nombre,$descripcion,$fecha,$duracion,$costo)
    {


        // Registra el tema
	    $control = \App\Actividad::create([
        'grupo_interes_id' => $grupo,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'fecha' => $fecha,
        'duracion' => $duracion,
        'costo' => $costo,
        'estado' => 'P'
                  ]);
        
        $this->say('Se creo la actividad '.$this->nombre);          
        $this->bot->startConversation(new AdministrarSistemaConversacion());

    }
    private function validarEntero($cadena)
    {
    if(is_numeric($cadena)) {
        return true;
    } else {
        return false;
    }
    }
    private function validarFecha($cadena)
    {
        // Procesar la fecha recibida para obtener sus partes
        $partes = explode ("/", $cadena);
    
        // Verificar que tenga tres partes (d/m/a)
        if(count($partes) != 3)
                return false;
    
        // Verifcar que sea una fecha válida
        $control = checkdate($partes[1], $partes[0], $partes[2]);
     
        if(!$control)
                return false;
    
            $ahora = time();
            $fecha = mktime(23, 59, 59, intval($partes[1]), intval($partes[0]), intval($partes[2]));
    
            // Verificar que sea una fecha futura
            if ($fecha < $ahora)
                return false;
    
        return true;
    }
       


}

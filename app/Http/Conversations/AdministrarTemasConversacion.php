<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class AdministrarTemasConversacion extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->mostrarMenuTemas();
    }
   /**
     * Muestra el menú para gestionar los temas.
     *
     * 
     */
    public function mostrarMenuTemas()
    {
        // Preparar los botones de acuerdo con los servicios existentes
        $botones = array();
        $botones[] = Button::create('Consultar temas')->value(1);
        $botones[] = Button::create('Crear un tema')->value(2);
        $botones[] = Button::create('Volver al menú de administración')->value(3);

        $cualOpcion = Question::create("Elige la operación que quieres realizar")->addButtons($botones);

        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                switch($this->opcion)
                {
                    case 1: 
                        $this->consultarTemas();
                        break;
                    case 2: 
                        $this->confirmarCreacionTema();
                        break;
                    case 3: 
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
     * Esta función permit consultar los temas
     */
    public function consultarTemas()
    {
        // Se consultar todos los temas
        $temas = \App\Tema::orderBy('nombre', 'asc')->get();

        // Mostrar los temas uno por uno
        $botones = array();
        foreach($temas as $tema)
        {
            $botones[] = Button::create($tema->nombre)->value($tema->id);
        }

        if(count($temas) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, en este momento no hay temas .");
        }
        else
        {
            $cualOpcion = Question::create("Los siguientes son los temas, elige uno para ver más opciones")->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->tema_id = $answer->getValue();
                    $this->mostrarInformacionTema();
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
     * Muestra la información de un tema y permite editarlo o eliminarlo
     */
    public function mostrarInformacionTema()
    {
        // Obtener la información del tema seleccionado
        $this->tema = \App\Tema::find($this->tema_id);

        $botones = array();
        $botones[] = Button::create('Editar')->value(1);
        $botones[] = Button::create('Borrar')->value(2);
        $botones[] = Button::create('Nada')->value(3);

        $cualOpcion = Question::create("Selecciona la operación que quieres realizar sobre el tema {$this->tema->nombre}")->addButtons($botones);

        $this->bot->typesAndWaits(1);
        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                if($this->opcion == 1)
                {
                    $this->ask("¿Cual es el nuevo nombre del tema?", function (Answer $answer)
                    {
                        $this->tema->nombre = $answer->getText();
                        $this->tema->save();

                        $this->bot->typesAndWaits(1);
                        $this->say('He actualizado el tema.');
                        $this->mostrarMenuTemas();
                    });
                }
                else if ($this->opcion  == 2)
                {
                    $grupos = \App\GrupoInteres::where('tema_id', $this->tema->id)->get();
                    if(count($grupos) > 0)
                    {
                            $this->bot->reply("Ups, el tema tiene grupos de interés relacionados, no es posible borrarlo.");
                            $this->mostrarMenuTemas(); 
                    
                    }else{


                        $confirmacion = Question::create('¿Estás seguro de que deseas realmente borrar este tema?'.$this->tema->nombre)
                        ->addButtons([
                            Button::create('Confirmar')->value('si'),
                            Button::create('Cancelar')->value('no')
                        ]);
               
                    $this->ask($confirmacion, function (Answer $answer2) {
                        if ($answer2->isInteractiveMessageReply()) {
                            $opcion = $answer2->getValue();
                        
                            if($opcion == "si")
                            {
                                $this->say('Entendido, voy a borrar el tema '.$this->tema->nombre);
                                
                                $this->say('Se borró el tema...');
                                $this->tema->where('nombre',$this->tema->nombre)->delete();
                                $this->mostrarMenuTemas(); 


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
     *Esta función confirma la creación de un tema 
     */
    public function confirmarCreacionTema()
    {

        $this->ask("¿Cuál será el nombre del nuevo tema?", function (Answer $answer)
        {   
                $this->nombre = $answer->getText();

                $confirmacion = Question::create('¿Estás seguro de que deseas agregar este tema?'.$this->nombre)
            ->addButtons([
                Button::create('Confirmar')->value('si'),
                Button::create('Cancelar')->value('no')
            ]);

        $this->ask($confirmacion, function (Answer $answer2) {
            if ($answer2->isInteractiveMessageReply()) {
                $opcion = $answer2->getValue();
            
                if($opcion == "si")
                {
                    $this->say('Entendido, voy a agregar el tema '.$this->nombre);
                    $this->crearTema($this->nombre);
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
               
    
         
        });
    
  
    }

    /**
     * Esta función permite crear los temas
     */
    public function crearTema($nombre)
    {


        // Registra el tema
	    $control = \App\Tema::create([
        'nombre' => $nombre
                  ]);
        
        $this->say('Se creo el tema '.$this->nombre);          
        $this->mostrarMenuTemas();

    }


}

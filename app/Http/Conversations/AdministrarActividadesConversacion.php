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
        // Se consultar todos los temas
        $grupos = \App\GrupoInteres::orderBy('tema_id', 'asc')->get();

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
     * Muestra la información de un grupotema y permite luego listar sus actividades
     */
    public function mostrarInformacionGrupo()
    {
        // Obtener la información del tema seleccionado
        $this->grupo = \App\GrupoInteres::find($this->id);

        $botones = array();
        $botones[] = Button::create('Listar activiades para cambiar su estado')->value(1);
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
                   /* $this->ask("¿Cual es el nuevo nombre del tema?", function (Answer $answer)
                    {
                        $this->tema->nombre = $answer->getText();
                        $this->tema->save();

                        $this->bot->typesAndWaits(1);
                        $this->say('He actualizado el tema.');
                        $this->mostrarMenuTemas();
                    });*/
                }
                else if ($this->opcion  == 2)
                {
                   /* $grupos = \App\GrupoInteres::where('tema_id', $this->tema->id)->get();
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


                    }*/
                

                    
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


}

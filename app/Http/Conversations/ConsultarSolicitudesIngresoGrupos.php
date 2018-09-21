<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Illuminate\Support\Facades\DB;

class ConsultarSolicitudesIngresoGrupos extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //Identificador del usuario en el chat
        $this->id = $this->bot->getUser()->getId();
        
        //Se obtiene la persona asociada a dicho identificador de usuario
        $this->persona = \App\Persona::where('codigo', $this->id)->first();
        $this->mostrarTemas();
    }

    /**
     * Muestra los temas de los grupos de interés
     */
    public function mostrarTemas()
    {
        // Obtener los temas almacenados en la base de datos
        $temas = \App\Tema::orderBy('nombre', 'asc')->get();
        
        // Mostrar los temas uno por uno
        $botones = array();
        foreach($temas as $tema)
        {
            $botones[] = Button::create($tema->nombre)->value($tema->id);
        }

        if(count($temas) == 0)
        {
                $this->bot->reply("Ups, no tengo temas disponibles.");
        }
        else
        {
            $cualOpcion = Question::create("Selecciona uno de los siguientes temas par ver los grupos de interés")->addButtons($botones);

            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->tema_id = $answer->getValue();
                    $this->mostrarGrupos();
                }
                else
                {
                    // Si el usuario digitó su respuesta como texto
                    $this->say('Por favor elige una opción de la lista.');
                    $this->repeat();
                }

            });
        }
    }

    /**
     * Muestra los grupos asociados al tema seleccionado
     */
    public function mostrarGrupos()
    {
        // Obtener los servicios almacenados en la base de datos
        $tema = \App\Tema::find($this->tema_id);

        // Se consultar los grupos según el tema seleccionado
        $gruposInteres = \App\GrupoInteres::where('tema_id', $this->tema_id)->orderBy('nombre', 'asc')->get();

        //$this->say('El tema seleccionado es: ' . $tema->nombre . ' y hay ' . count($gruposInteres) . ' grupos');

        // Mostrar los grupos uno por uno
        $botones = array();
        foreach($gruposInteres as $grupo)
        {
            $botones[] = Button::create($grupo->nombre)->value($grupo->id);
        }

        if(count($gruposInteres) == 0)
        {
                $this->bot->reply("Ups, este tema no tiene grupos de interés .");
        }
        else
        {
            $cualOpcion = Question::create("Los siguientes son los grupos de interés asociados a " . $tema->nombre . ".  Selecciona uno para ver la solicitud")->addButtons($botones);

            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->grupo_id = $answer->getValue();
                    $this->mostrarSolicitudes();
                }
                else
                {
                    // Si el usuario digitó su respuesta como texto
                    $this->say('Por favor elige una opción de la lista.');
                    $this->repeat();
                }

            });
        }

    }

    /**
     * Muestra las solicitudes de los grupos de interés
     */
    public function mostrarSolicitudes()
    {
        // Obtener los temas almacenados en la base de datos
        $solicitudesIngreso = 
        DB::table('solicitudes_ingreso')
        ->select('solicitudes_ingreso.id', 'personas.nombres', 'personas.apellidos', 'grupos_interes.nombre')
        ->distinct()
        ->join('personas', 'solicitudes_ingreso.persona_id', '=', 'personas.id')
        ->join('grupos_interes', 'solicitudes_ingreso.grupo_interes_id', '=', 'grupos_interes.id')
        ->where('solicitudes_ingreso.estado', '<>', 1)
        ->where('solicitudes_ingreso.grupo_interes_id', '=', $this->grupo_id)
        ->get();

        // Mostrar los temas uno por uno
        $botones = array();
        foreach($solicitudesIngreso as $solicitud)
        {
            $botones[] = Button::create($solicitud->nombres)->value($solicitud->id);
        }

        if(count($solicitudesIngreso) == 0)
        {
                $this->bot->reply("Tranquil@, no tienes solicitudes pendientes.");
        }
        else
        {
            $cualOpcion = Question::create("Selecciona uno de las siguientes solicitudes de ingreso")->addButtons($botones);

            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->id_solicitudes_ingreso = $answer->getValue();
                    $this->aceptarSolicitud();
                }
                else
                {
                    // Si el usuario digitó su respuesta como texto
                    $this->say('Por favor elige una opción de la lista.');
                    $this->repeat();
                }

            });
        }
    }

    public function aceptarSolicitud()
    {
        $botones = array(
            Button::create('Si')->value('S'),
            Button::create('No')->value('N')
        );
        
        $opcionAceptar = Question::create("Aceptar o rechazar ingreso?")->addButtons($botones);
        $this->ask($opcionAceptar, function (Answer $answer)
        {
            if ($answer->isInteractiveMessageReply())
            {
                $respuesta = $answer->getValue();
                switch ($respuesta) {
                    case 'S':
                        DB::table('solicitudes_ingreso')
                        ->where('id', $this->id_solicitudes_ingreso)
                        ->update(['estado' => 1]);
                        break;
                    case 'N':
                        DB::table('solicitudes_ingreso')
                        ->where('id', $this->id_solicitudes_ingreso)
                        ->update(['estado' => 2]);
                        break;
                    default:
                        $this->say('Opción no valida.');
                        break;
                }
                $this->say('Se ha actulizado la solicitud.');

                $opcionAceptar = Question::create("Desea ver otra solicitud?")->addButtons($botones);
                $this->ask($opcionAceptar, function (Answer $answer)
                {
                    if ($answer->isInteractiveMessageReply())
                    {
                        $respuesta = $answer->getValue();
                        switch ($respuesta) {
                            case 'S':
                                $this->mostrarTemas();
                                break;
                            case 'N':
                                $bot->startConversation(new App\Http\Conversations\MenuPrincipalConversacion());
                                break;
                            default:
                                $this->say('Opción no valida.');
                                break;
                        }
                    }
                });
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
     * Retorna al menú principal del chat
     */
    public function ayudarAlgoMas()
    {
        //Asignar variable al user storage para tenerla disponible en otra conversación
        $this->bot->userStorage()->save([
            'ayuda_algo_mas' => 'S'
        ]);

        $this->bot->startConversation(new MenuPrincipalConversacion());
    }


}

<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class ProximasActividadesConversacion extends Conversation
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

        $userinformation = $this->bot->userStorage()->find($this->id);
        $element = $userinformation->get('grupo_interes_id');

        if($element != '')
        {
            $this->grupo_id = $element;
            
            $this->bot->userStorage()->save([
                'grupo_interes_id' => ''
            ]);

            $this->mostrarProximasActividades();
        }
        else
        {
            $this->mostrarGrupos();    
        }
    }

    /**
     * Muestra los grupos asociados al tema seleccionado
     */
    public function mostrarGrupos()
    {
        // Se consultar los grupos según el tema seleccionado
        $gruposInteres = \App\GrupoInteres::orderBy('nombre', 'asc')->get();

        // Mostrar los grupos uno por uno
        $botones = array();
        foreach($gruposInteres as $grupo)
        {
            $botones[] = Button::create($grupo->nombre)->value($grupo->id);
        }

        if(count($gruposInteres) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, no tenemos grupos de interés .");
        }
        else
        {
            $cualOpcion = Question::create("Selecciona un grupo para ver sus próximas actividades")->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->grupo_id = $answer->getValue();
                    $this->mostrarProximasActividades();
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
     * Muestra las próximas actividades 
     */
    public function mostrarProximasActividades()
    {
        //Obtener información del grupo de interés
        $grupoInteres = \App\GrupoInteres::find($this->grupo_id);

        // Obtener los actividades almacenados en la base de datos
        $actividades = \App\Actividad::where('grupo_interes_id', $this->grupo_id)->orderBy('nombre', 'asc')->get();
        
        // Mostrar los actividades uno por uno
        $botones = array();
        foreach($actividades as $actividad)
        {
            $botones[] = Button::create($actividad->nombre)->value($actividad->id);
        }

        if(count($actividades) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, no tengo actividades disponibles.");
        }
        else
        {
            $cualOpcion = Question::create("Estas son las próximas actividades del grupo " . $grupoInteres->nombre . ", elige una para ver mayor información")->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->actividad_id = $answer->getValue();
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
     * Muestra la información de la actividad seleccionada
     */
    public function mostrarInformacionActividad()
    {
        //Obtener información de la actividad seleccionada
        $actividad = \App\Actividad::find($this->actividad_id);

        $this->bot->typesAndWaits(1);
        $this->say($actividad->nombre . ": " . $actividad->descripcion);
        $this->bot->typesAndWaits(2);
        $this->say('Se realizará en la fecha ' . $actividad->fecha . ' con una duración de ' . $actividad->duracion . ' minutos y un costo de $' . $actividad->costo);

        /*
        //Se consulta si el usuario ya tiene una solicitud de ingreso al grupo
        $gruposInteres = \App\SolicitudIngreso::where(
            [
                ['persona_id', '=', $this->persona->id],
                ['grupo_interes_id', '=', $this->grupo_id]
            ])->whereIn('estado', ['A', 'P'])->first();

        if(count($gruposInteres) == 0)    
        {
            $this->solicitarIngreso();
        }
        else
        {
            $this->bot->typesAndWaits(1);
            $this->say('Tu ya tienes una solicitud de ingreso para este grupo.');
        }
        */

    }
}

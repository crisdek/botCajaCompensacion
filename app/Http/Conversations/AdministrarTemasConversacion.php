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
                        $this->crearTema();
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
     * Muestra la información de un grupo y permite editarlo o eliminarlo
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
                    });
                }
                else if ($this->opcion  == 2)
                {
                    $this->bot->typesAndWaits(1);
                    $this->say('Bueno, tal vez en una próxima ocasión.');
                }
                else
                {

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

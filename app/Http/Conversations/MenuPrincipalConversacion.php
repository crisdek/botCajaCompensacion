<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;


class MenuPrincipalConversacion extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->mostrarMenuPrincipal();
    }

    public function mostrarMenuPrincipal()
    {
        // Preparar los botones de acuerdo con los servicios existentes
        $botones = array();
        $botones[] = Button::create('Explorar grupos de interés')->value(1);
        $botones[] = Button::create('Próximas actividades')->value(2);
        $botones[] = Button::create('Administrar sistema')->value(3);
        $botones[] = Button::create('Nada')->value(4);

        $cualOpcion = Question::create("Selecciona una de las siguientes opciones")->addButtons($botones);

        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                switch($this->opcion)
                {
                    case 1: 
                        $bot->startConversation(new App\Http\Conversations\MenuPrincipalConversacion());
                        break;
                    case 2: 
                        $bot->startConversation(new App\Http\Conversations\ExplorarGruposConversacion());
                        break;
                    case 3: 
                        $bot->startConversation(new App\Http\Conversations\AdministrarSistemaConversacion());
                        break;
                    case 4: 
                        $this->say('Ha sido un placer tenerte por aquí, vuelve cuando quieras.');
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
    
}

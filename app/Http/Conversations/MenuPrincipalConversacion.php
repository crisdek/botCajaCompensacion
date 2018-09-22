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
        $this->id = $this->bot->getUser()->getId();
        $userinformation = $this->bot->userStorage()->find($this->id);
        $element = $userinformation->get('ayuda_algo_mas');

        if($element == 'S')
        {
            $this->bot->typesAndWaits(1);
            $this->say('¿Te puedo ayudar en algo más?');

            $this->bot->userStorage()->save([
                'ayuda_algo_mas' => 'N'
            ]);
        }

        $this->mostrarMenuPrincipal();
    }

    /**
     * Muestra las opciones del menú principal
     */
    public function mostrarMenuPrincipal()
    {
        // Preparar los botones de acuerdo con los servicios existentes
        $botones = array();
        $botones[] = Button::create('Explorar grupos de interés')->value(1);
        $botones[] = Button::create('Próximas actividades')->value(2);
        $botones[] = Button::create('Administrar sistema')->value(3);
        $botones[] = Button::create('Nada')->value(0);

        $cualOpcion = Question::create("Selecciona una de las siguientes opciones")->addButtons($botones);

        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                switch($this->opcion)
                {
                    case 1: 
                        $this->bot->startConversation(new ExplorarGruposConversacion());
                        break;
                    case 2: 
                        $this->bot->startConversation(new ProximasActividadesConversacion());
                        break;
                    case 3: 
                        $this->bot->startConversation(new AdministrarSistemaConversacion());
                        break;
                    default:
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

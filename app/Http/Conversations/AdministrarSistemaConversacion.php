<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class AdministrarSistemaConversacion extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->mostrarMenuAdministrador();
    }

/**
     * Muestra las opciones del menú administrador
     */
    public function mostrarMenuAdministrador()
    {
        // Preparar los botones de acuerdo con los servicios existentes
        $botones = array();
        $botones[] = Button::create('Consultar solicitudes')->value(1);
        $botones[] = Button::create('Administrar temas')->value(2);
        $botones[] = Button::create('Administrar actividades')->value(3);
        $botones[] = Button::create('Ver las opciones anteriores')->value(4);

        $cualOpcion = Question::create("Selecciona una de las siguientes opciones de administrador")->addButtons($botones);

        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                switch($this->opcion)
                {
                    case 1: 
                    $this->bot->startConversation(new ConsultarSolicitudesIngresoGrupos());
                    break;
                    case 2: 
                        $this->bot->startConversation(new AdministrarTemasConversacion());
                        break;
                    case 3: 
                        $this->bot->startConversation(new AdministrarActividadesConversacion());
                        break;
                    case 4: 
                        $this->bot->startConversation(new MenuPrincipalConversacion());
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

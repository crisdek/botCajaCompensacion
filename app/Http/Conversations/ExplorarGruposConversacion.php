<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class ExplorarGruposConversacion extends Conversation
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

        //Forma de recuperar información almacenada en otra conversación
        /*
        $userinformation = $this->bot->userStorage()->find($this->id);
        $element = $userinformation->get('trabajo');

        $this->say('trabajas en: ' . $element);
        */
        
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
            $this->bot->typesAndWaits(1);            
            $this->bot->reply("Ups, no tengo temas disponibles.");
        }
        else
        {
            $cualOpcion = Question::create("Selecciona uno de los siguientes temas par ver los grupos de interés")->addButtons($botones);

            $this->bot->typesAndWaits(1);
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
                    $this->bot->typesAndWaits(1);
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

        // Mostrar los grupos uno por uno
        $botones = array();
        foreach($gruposInteres as $grupo)
        {
            $botones[] = Button::create($grupo->nombre)->value($grupo->id);
        }

        if(count($gruposInteres) == 0)
        {
            $this->bot->typesAndWaits(1);
            $this->bot->reply("Ups, este tema no tiene grupos de interés .");
        }
        else
        {
            $cualOpcion = Question::create("Los siguientes son los grupos de interés asociados a " . $tema->nombre . ".  Selecciona uno para ver más información")->addButtons($botones);

            $this->bot->typesAndWaits(1);
            $this->ask($cualOpcion, function (Answer $answer)
            {   
                if ($answer->isInteractiveMessageReply())
                {
                    $this->grupo_id = $answer->getValue();
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
     * Muestra la información del grupo seleccionado
     */
    public function mostrarInformacionGrupo()
    {
        
        //Obtener informaciónd el grupo seleccionado
        $grupo = \App\GrupoInteres::find($this->grupo_id);

        $this->bot->typesAndWaits(1);
        $this->say('El grupo seleccionado es: ' . $grupo->nombre);
        $this->bot->typesAndWaits(1);
        $this->say( $grupo->descripcion);

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

    }

    /**
     * En caso de que el usuario no tenga una solicitud aceptada o pendiente
     * le permite realizar una solicitud de ingreso al grupo
     */
    public function solicitarIngreso()
    {
        $botones = array();
        $botones[] = Button::create('Si')->value('S');
        $botones[] = Button::create('No')->value('N');

        $cualOpcion = Question::create("¿Te gustaría ingresar al grupo?")->addButtons($botones);

        $this->bot->typesAndWaits(1);
        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                if($this->opcion == 'S')
                {
                    $solicitudIngreso = \App\SolicitudIngreso::firstOrNew(array(
                        'persona_id' => $this->persona->id,
                        'grupo_interes_id' => $this->grupo_id,
                        'estado' => 'P'
                    ));
                    $solicitudIngreso->save();
        
                    $this->bot->typesAndWaits(1);
                    $this->say('He registrado tu solicitud, nuestro equipo la revisará y te informaremos sobre el resultado');
                }
                else
                {
                    $this->bot->typesAndWaits(1);
                    $this->say('Bueno, tal vez en una próxima ocasión.');
                }

                $this->verActividadesDelGrupo();
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
     * Mostrar conversación para ver próximas actividades
     */
    public function verActividadesDelGrupo()
    {
        $botones = array();
        $botones[] = Button::create('Si')->value('S');
        $botones[] = Button::create('No')->value('N');

        $cualOpcion = Question::create("¿Quieres ver las actividades asociadas a este grupo?")->addButtons($botones);

        $this->bot->typesAndWaits(1);
        $this->ask($cualOpcion, function (Answer $answer)
        {   
            if ($answer->isInteractiveMessageReply())
            {
                $this->opcion = $answer->getValue();

                if($this->opcion == 'S')
                {
                    //Asignar variable al user storage para tenerla disponible en otra conversación
                    $this->bot->userStorage()->save([
                        'grupo_interes_id' => $this->grupo_id
                    ]);

                    //Llamar a conversación para ver próximas actividades
                    $this->bot->startConversation(new ProximasActividadesConversacion());
            
                }
                else
                {
                    //Llamar conversación para te puedo ayudar en algo más
                    $this->ayudarAlgoMas();
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

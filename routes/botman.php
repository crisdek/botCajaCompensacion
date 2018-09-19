<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('/start', function ($bot) {

	// Obtener la información del usuario en sesión
	$user = $bot->getUser();
	$id = $user->getId();
	$username = $user->getUsername() ?: "desconocido";
	$firstname = $user->getFirstName() ?: "desconocido";
    $lastname = $user->getLastName() ?: "desconocido";
    
    // Forma de almacenar variables para pasar entre conversaciones
    // Ver como se recupera dentro de ExplorarGruposConversacion
    /*
    $bot->userStorage()->save([
        'trabajo' => 'Desarrollo de software'
    ]);
    */

	// Crear o actualizar la información del usuario en sesión
	$persona = \App\Persona::firstOrNew(array(
        'codigo' => $id,
        'administrador' => 'N',
    	'nombre_usuario' => $username,
    	'nombres' => $firstname,
    	'apellidos' => $lastname
	));
	$persona->save();

	// Mostrar mensaje de bienvenida
    $bot->reply("Hola $firstname, me alegra mucho que uses nuestros servicios.");
    //$bot->typesAndWaits(15);
    $bot->reply("¿En que te puedo ayudar?");

    $bot->startConversation(new App\Http\Conversations\MenuPrincipalConversacion());
});

$botman->fallback(function($bot) {
    $bot->reply('Lo siento, No entiendo este comando. Puedes ingresar /start para iniciar una conversación');
});


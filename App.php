<?php
//tut http://socketo.me/docs/hello-world
//magic sauce  composer dump-autoload -o 

/*
    TODO
        - UI : monday, 1pt
        - functional and unit tests : moday-tues  2pt PHPunit
        https://docs.docker.com/language/java/run-tests/
        - dev envo : wensday 3pt
        
        bonus:
            code quality,Error Handling : 1
            code documentation : 1
            fun spin           : 5
            security           : 1

    nginx for serving
        - dont fuck it up :D
        -https://medium.com/@hack4mer/how-to-setup-wss-for-ratchet-websocket-server-on-nginx-or-apache-c5061229860a
*/

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Requests;

    //autoloading namespace
    require dirname(__FILE__) . '/vendor/autoload.php';

    //making server with socket type
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Requests()
            )
        ),
        8080
    );

    $server->run();

/*
//https://www.php.net/manual/en/class.splobjectstorage.php
//nice map from obj to data, kinda like a hash table for obj's

/*
potinatal optimizations/problems 

    - to many sockets   NEED TODO
    https://tsh.io/blog/how-to-scale-websocket/
    - doing nothing on open
    - alot of writing back and fourth with store obj
*/

/*
from my understanding this class is used once 
and only once within the app.php
its kinda like a service template so its not makeing 
multiple instnaces
*/
?>

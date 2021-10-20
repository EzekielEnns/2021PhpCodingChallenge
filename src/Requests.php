<?php 

//wil be using ratchet http://socketo.me/
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use MyApp\Process;

//https://www.php.net/manual/en/class.reflectionclass.php
class Requests implements MessageComponentInterface {
    protected $process;

    public function __constructor(){
        $this->process = new Process(2,'MyApp\TicTacToe');
    }

    //on first connection
    public function onOpen(ConnectionInterface $conn){
        echo "({$conn->resourceId} connected)\n";
        $this->process->onOpen($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg){
        $msg = json_decode($msg);
        if(json_last_error() == JSON_ERROR_NONE){
            $result = $this->process->onMessage($from,$msg);
            $msg = json_encode($result['msg']);
            foreach ($result['Clients'] as $client) {
                $client->send($msg);
            }
        }
        else{
            $msg = new \stdClass;
            $msg->error = 'Bad Json';
            $from->send(json_encode($msg));
        }
    }

    public function onClose(ConnectionInterface $conn){
        $this->process->onClose($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e){
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();      
    }
}

?>
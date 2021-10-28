<?php 
//This class needs to be remastered to fit more generalized
//messages

//this class is the implementation of the Socket Library Rachets
//it validates incoming messages and sends data to processing
//TODO implement security by checking origin

//socket library is ratchet http://socketo.me/
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use MyApp\Process;

class Requests implements MessageComponentInterface {
    protected $process;

    public function __construct(){
        $this->process = new Process(2,'MyApp\TicTacToe');
    }

    //TODO check if origin is valid
    public function onOpen(ConnectionInterface $conn){
        echo "({$conn->resourceId} connected)\n";
    }

    //validates message's and sends an error if bad
    //also sends response back after processing
    public function onMessage(ConnectionInterface $from, $msg){
        echo 'Msg recvied';
        $msg = json_decode($msg);
        if(json_last_error() == JSON_ERROR_NONE){
            $result = $this->process->onMessage($from,$msg);
            $msg = json_encode($result['msg']);

            //sending message back to all clients
            foreach ($result['Clients'] as $client) {
                $client->send($msg);
            }
            echo "\r\nsent msg\r\n";
        }
        else{
            //TODO wanna stream line this with processing errors 
            //probably wont tho
            $msg = new \stdClass;
            $msg->error = 'Bad Json';
            $from->send(json_encode($msg));
            echo "\r\nsent error";
        }
    }


    //on close and on error do the same thing since we gotta yeet those bad clients
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
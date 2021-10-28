<?php
//this class dose all the message processing 
//it needs to be reworked to deal with more generalized messages
//my plan is to implement something similar to a REST API
//and have clear display of different message types

namespace MyApp;
use Ratchet\ConnectionInterface;
use MyApp\MatchMaker;

//supporting classes that contain useful data
//these will be changed to utilize more general use
//don't know how yet though

//user data ofcourse
class User{
    public $name; 
    public $score = 0;  //player score
    public $marker = 1; //x or o
    private $conn;
    public function __construct($name,$conn){
        $this->name = $name;
        $this->conn = $conn;
    }

    //special updater method
    public function initMarker(&$itter){
        $this->marker+=$itter;
        $itter++;
    }
    public function getConn(){return $this->conn;}
}

//data that is sent back and forth between players
class Data{
    public $won = false;
    public $board = null;
    public $done = false;
    public $players = [];
}

class Process{
    protected $matches;     //matches
    protected $userData;    //stores client info (score,names...etc)
    
    //providing read access 
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
      }

    public function __construct(int $capacity, $obj){
        $this->userData = [];
        $this->matches = new MatchMaker($capacity,$obj);
    }

    //clean up when a user is gone
    public function onClose($conn){
        $this->matches->removeActive($conn);
        $this->matches->removeLobby($conn);
    }

    //this should all be remastered
    //the bread and butter
    //takes a object that is valid and returns 
    //an array of clients and an object to send them
    public function onMessage($from, $msg){
        $key = spl_object_hash($from);
        $clients = [];
        $send = new Data();

        //check for valid request
        if($this->isValidInput($msg)){
            //adding new user data
            if(!isset($this->userData[$key])){
                $this->userData[$key]= new User($msg->name,$from);
            }
            
            //adding client to match
            $newActive = !$this->matches->inLobby($from); //if active then itll be true
            $this->matches->add($from);
            $newActive = $newActive && !$this->matches->inLobby($from)? true:false;
            $match = $this->matches->get($from);
            //TODO send active game info
            //also
            //when in active match
            if(!$this->matches->inLobby($from)){
                echo "\r\nFound an active game\r\n";
                //play on there turn
                if($match['party'][0] == $key){
                    //do game operations
                    if($match['game']->update($msg->action)){
                        $send->won = $match['game']->Win($msg->action->v);
                        
                        $increment = 0;
                        //building list of clients 
                        foreach ($match['party'] as $client) {
                            $send->players[] = $this->userData[$client];
                            $clients[] = $this->userData[$client]->getConn();
                            
                            //removing the finished match
                            if($send->won){
                                $this->matches->removeActive($client);
                            }
                            if($newActive)$this->userData[$client]->initMarker($increment);
                        }
                    }
                    else{
                        $send->error = "Update didn't work";    
                    }
                    //we gonna be mean
                    $this->matches->endTurn($from);
                }
                //its not their turn
                else{
                    $send->error = "not your turn";
                }
            }
            //they are still in lobby
            else{
                echo "\r\nInLobby\r\n";
                $match['game']->update($msg->action); //TODO ERROR this didnt actually store itself
                $send->won = $match['game']->Win($msg->action->v);
                
                
                $send->players[] = (object)['name'=>'Looking...'];
            }

            //packing up game data
            $send->board = $match["game"]->getBoard()->getValues();
            $send->done = $match['game']->isDone();
        }
        else{
            $send->error = 'you sent a bad msg';
        }
        
        //filing clients if not already filled
        if(count($clients) == 0){
            $clients[] = $from;
        }
        return ['Clients'=>$clients,'msg'=>$send];
    }

    //dose not need test because it is 
    //determine if input is good for sendMessage
    public function isValidInput($in){
        if(is_object($in)){
            if(isset($in->name)){
                if(isset($in->action)){
                    return true;
                }
            }
        }
        return false;
    }
}

?>
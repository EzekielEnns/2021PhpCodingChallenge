<?php
//a class that conatins socket processesing logic 
namespace MyApp;
use Ratchet\ConnectionInterface;
use MyApp\MatchMaker;

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
        $this->userData = new \SplObjectStorage;
        $this->matches = new MatchMaker($capacity,$obj);
    }

    public function onOpen($conn){
        $this->userData->attach($conn);
    }

    public function onClose($conn){
        $this->matches->removeActive($conn);
        $this->matches->removeLobby($conn);
    }

    //the bread and butter
    //takes a object that is valid and returns 
    //an array of clients and an object to send them
    public function onMessage($from, $msg){
        $clients = [];
        $send = new \stdClass; //msg to be sent back in json
        /*
        send = {
            board = array of values
            done = bool
            won = bool
            names = array of strings
        }
        clients = array of objects
        */
        //check for valid request
        if($this->isValidInput($msg)){
            //adding new user data
            if(!$this->userData->offsetExists($from)){
                $this->userData[$from]= ['name'=>$msg->name];
            }
            
            //adding client to match
            $this->matches->add($from);
            $match = $this->matches->get($from);
            
            //when in active match
            if(count($match['party']) != 0){
                //play on there turn
                if($match['party'][0] == $from){
                    //do game operations
                    if($match['game']->update($msg->action)){
                        $send->won = $match['game']->Win($msg->action->v);
                        
                        $this->matches->endTurn($from);
                        
                        //building list of clients 
                        foreach ($match['party'] as $client) {
                            $send->names[] = $this->userData[$client]['name'];
                            $clients[] = $client;
                            
                            //removing the finished match
                            if($send->won){
                                $this->matches->removeActive($client);
                            }
                        }
                    }
                    else{
                        $send->error = "Update didn't work";    
                    }
                }
                //its not their turn
                else{
                    $send->error = "not your turn";
                }
            }
            //they are still in lobby
            else{
                $match['game']->update($msg->action);
                //$match['game']->bot(); TODO
                $send->names[] = 'looking for match';
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
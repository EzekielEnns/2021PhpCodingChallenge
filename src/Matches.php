<?php
namespace MyApp;

/*
this class facilitates matches between players
    it could be split into two classes 
    one for lobby and one for active 
    but for now it serves its purpose
*/
class MatchMaker{
    private $active;    //players in active multiplayer games
    private $lobby;     //players waiting for an active match
                        //they will play inactive matches 
    private $partyCapacity;  //how many people can be in a match
    private $gameClass;     //the obj that will be made every match
    //TODO add getter
    public function __construct($partyCapacity, $gameClass){
        $this->active = new \SplObjectStorage;
        $this->lobby = new \SplObjectStorage;
        $this->partyCapacity = $partyCapacity;
        $this->gameClass = $gameClass;
    }

    //returns values for connection
    public function get($conn){
        if($this->active->offsetExists($conn)){
            return $this->active[$conn];
        }
        elseif($this->lobby->offsetExists($conn)){
            return $this->lobby[$conn];
        }
        else{
            return null;
        }
    }

    //add connection to either active, if not then lobby 
    public function add($conn){
        //if not already in an active match
        if(!$this->active->offsetExists($conn)){
            if(!$this->findActive($conn)){
                if(!$this->lobby->offsetExists($conn)){
                    $this->lobby[$conn] = ['game'=>new $this->gameClass,
                                           'party'=>[]];
                }
            }
        }
    }

    //shifts turns aka party array
    public function endTurn($conn){
        if($this->active->offsetExists($conn)){
            //this returns a value not a reff
            $match = $this->active[$conn]; //this is a reffernce value
            $last = array_shift($match['party']);
            array_push($match['party'],$last);
            foreach ($match['party'] as $player) {
                $this->active[$player] =  $match;
            }
        }
    }

    //returns true if player is in lobby
    public function inLobby($conn){
        return $this->lobby->offsetExists($conn);
    }

    //trys to find an active match
    public function findActive($conn){
        if(!$this->active->offsetExists($conn)){
            //seeing if there is enough payers to make a match with current
            if(count($this->lobby)+1 >= $this->partyCapacity){
                $this->lobby->detach($conn);
                $this->lobby->rewind();
                $party = [$conn];
                $partySize = 1;
                
                //making party with current memeber
                while($partySize < $this->partyCapacity){
                    $player = $this->lobby->current();
                    $this->lobby->detach($player);
                    $party[] = $player;
                    $partySize++;
                }

                $game = new $this->gameClass;
                $data = ['game'=>$game,'party'=>$party];
                //adding party game to active matches/clients
                foreach ($party as $player) {
                    //storing reffernce into obj
                    $this->active[$player] = $data;
                    
                }
                return true;
            }
            return false;
        }
    }

    //remvoes a connection from lobby and active matches
    public function removeActive($conn){
        if($this->active->offsetExists($conn)){
            $removed = $this->active[$conn];
            $this->active->detach($conn);
            foreach($removed['party'] as $player){
                if($this->active->offsetExists($player)){
                    $this->active->detach($player);
                }
                $game = new $this->gameClass;
                $this->lobby[$player] = ['game'=>$game,'party'=>[]];
            }
            
        }
    }

    public function removeLobby($conn){
    if($this->lobby->offsetExists($conn)){
            $this->lobby->detach($conn);
        }
    }

}
?>
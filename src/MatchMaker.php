<?php
//This Class manages game matches 
//objects that represent players/connections
//are hashed and thrown into 1 of two associated arrays
//each array contains a game object, the active array contains a list of players 
//that are currently all sharing a game


//Note to self splobjectStorage is meh
namespace MyApp;

class MatchMaker{
    private $active;    //players in active multiplayer games
    private $lobby;     //players waiting for an active match
                        //they will play inactive matches 
    //TODO move to game
    private $partyCapacity;  //how many people can be in a match
    private $gameClass;     //the obj that will be made every match
    //TODO add getter
    public function __construct($partyCapacity, $gameClass){
        $this->active = [];
        $this->lobby = [];
        $this->partyCapacity = $partyCapacity;
        $this->gameClass = $gameClass;
    }

    //returns values for connection
    public function get($conn){
        $key = spl_object_hash($conn);
        if(isset($this->active[$key])){
            return $this->active[$key];
        }
        elseif(isset($this->lobby[$key])){
            return $this->lobby[$key];
        }
        else{
            return null;
        }
    }

    //add connection to either active, if not then to lobby 
    public function add($conn){
        $key = spl_object_hash($conn);
        //if not already in an active match
        if(!isset($this->active[$key])){
            //if couldnt find an active game
            if(!$this->findActive($conn)){

                if(!isset($this->lobby[$key])){
                    
                    $this->addToLobby($key);
                }
            }
        }
    }

    //shifts turns aka party array
    public function endTurn($conn){
        $key = spl_object_hash($conn);
        if(isset($this->active[$key])){
            $match = $this->active[$key];
            $last = array_shift($match['party']);
            array_push($match['party'],$last);
            foreach ($match['party'] as $player) {
                $this->active[$player] =  $match;
            }
        }
    }

    //returns true if player is in lobby
    public function inLobby($conn){
        $key = spl_object_hash($conn); 
        return isset($this->lobby[$key]);
    }

    public function removeLobby($conn){
        $key = spl_object_hash($conn);  
        if(isset($this->lobby[$key])){
            unset($this->lobby[$key]);
        }
    }

    private function addToLobby($key){
        if(!isset($this->lobby[$key])){
            $game = new $this->gameClass;
            $this->lobby[$key] = ['game'=>$game,'party'=>[]];
        }
    }

    //trys to find an active match
    public function findActive($conn,$lobbySort=null){
        $key = spl_object_hash($conn);
        if(!isset($this->active[$key])){
           
            //seeing if there is enough payers to make a match with current
            //we subtract 1 because we cant have match with this player
            //TODO this was sme bad code that was messing the whole thing up
            //if the player is always in lobby that means we 1 additonal player in the lobby
            $playerNum = isset($this->lobby[$key])?1:-1;
            echo"\r\n LobbyCount::".strval(count($this->lobby))."::"
                    .strval($playerNum)."\r\n";
            if(count($this->lobby)-$playerNum >= $this->partyCapacity){
                
                //removing from lobby and adding to party
                if(!isset($this->lobby[$key])){
                    unset($this->lobby[$key]); 
                    $party = [$key];
                }
                if(is_callable($lobbySort)){
                    $lobbySort($this->lobby);
                }
                $partySize = 1;
                //making party with current member
                while($partySize < $this->partyCapacity){

                    //getting player data
                    reset($this->lobby);
                    $player = key($this->lobby);
                    
                    
                    unset($this->lobby[$player]);
                    //
                    
                    //building party
                    $party[] = $player;
                    $partySize++;
                }
                //
                $game = new $this->gameClass;
                $data = ['game'=>$game,'party'=>$party];
                //adding party game to active matches/clients
                foreach ($party as $player) {
                    $this->active[$player] = $data;
                }
                //
                //
                
                return true;
            }
            return false;
        }
        return true;
    }

    //remvoes a connection from lobby and active matches
    public function removeActive($conn){
        $key = is_object($conn)?spl_object_hash($conn):$conn;
        if(isset($this->active[$key])){
            $removed = $this->active[$key];
            unset($this->active[$key]);
            foreach($removed['party'] as $player){
                if(isset($this->active[$player])){
                    unset($this->active[$player]);
                }
                $this->addToLobby($player);
            }
            
        }
    }

}
?>
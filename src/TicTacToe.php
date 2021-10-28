<?php
namespace MyApp;

//since games can widely differ using interface instead of abstract
interface Game{
    public function update($data):bool;
}

class TicTacToe implements Game{
    private $done = false;
    private $board; //contains the game board
    private $size;
    public function __construct(){
        $this->size = 3;
        $this->board = new Board(
            [
                0=>[1,3],
                1=>[2,4],
                2=>[5],
                3=>[4,6],
                4=>[0,2,5,7],
                5=>[8],
                6=>[4],
                7=>[6],
                8=>[4,7]
            ],
            array_fill(0,9,0)
        );
    }

    public function isDone():bool{
        return $this->done;
    }

    public function getBoard(){return $this->board;}
    //returns a string value
    public function update($data):bool{
        
        if(isset($data->i,$data->v)){
            //$row = $data->row;
            //$col = $data->col;
            $i = $data->i;
            if($i >= 0 && $i < $this->size**2){
            //if($i < $this->size && $row >= 0){
              //  if($col < $this->size && $col >= 0){
                    if($data->v == 1 ||$data->v == 2){
                        if($this->board[$i]/*[$row][$col]*/ == 0){
                            $this->board[$i]/*[$row][$col]*/ = $data->v;
                            return true;
                        }
                    }
                //}
            }
        }
        return false;
    }
    
    //breath for search
    public function win($value){
        //all win conditions, TODO calculate this in a static constructor with size
        $conditions = [
                        0=>[6=>3,2=>1],
                        1=>[7=>4],
                        2=>[8=>5],
                        3=>[5=>4],
                        6=>[2=>4],
                        8=>[0=>4,6=>7]
                    ];
        //testing each conditions to see if a path starts and ends at
        foreach ($conditions as $start => $ends) {
            //we get the path which is really quick since if theres no 1s its returns
            $path = $this->getPath($start,$value);
            
            //winning path is going to have at least 2 nodes 
            if(count($path) >= 2){
                foreach ($ends as $end=>$parent) {
                    if(isset($path[$end])){
                        if($path[$end] == $parent){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function getPath($start,$value){
        //the ones already searched and visted
        if($this->board[$start] == $value){
            $search = [$start];
            $visited = [$start];
            $graph = $this->board->getGraph();  //contections
            
            $path = []; //tacks the last path
            while(count($search)!=0){
                $node = array_shift($search);
                //get adjacent nodes
                $adj = $graph[$node];
                foreach ($adj as $next) {
                    if(!in_array($next,$visited)){
                        //check if unvisited is valid
                        if($this->board[$next] == $value){
                            //add to search queue 
                            array_push($search,$next);
                            //add this to visted
                            $visited[] = $next;
                            //add to path with parent in node
                            $path[$next] = $node;
                        }
                    }
                }
            }
            return $path;
        }
        return [];
    }
    
}

/*
values are acessed like array 
with pathing logic done by said class using get graph
*/
class Board implements \ArrayAccess{
    private $values;   //contains all values in array
    private $graph;

    public function __construct(Array $graph,Array $values){
        if(count($graph)!=count($values)) throw new exception("Graph and values dont match");
        $this->graph = $graph;
        $this->values = $values;
    }

    public function getGraph(){
        return $this->graph;
    }
    public function getValues(){
        return $this->values;
    }
    public function offsetExists($offset){
        return isset($this->values[$offset]);
    }
    public function offsetGet($offset){
        return $this->values[$offset];
    }
    public function offsetSet($offset, $value): void{
        $this->values[$offset] = $value;
    }
    public function offsetUnset($offset): void{
        unset($this->values[$offset]);
    }
}

?>
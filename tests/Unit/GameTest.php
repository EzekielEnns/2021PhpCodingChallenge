<?php 
namespace MyApp;
use PHPUnit\Framework\TestCase;

class TicTacToeTest extends TestCase{
    protected $update;
    protected function setUp(): void
    {
        $this->update = new \stdClass;
        //$this->update->row = 0;
        //$this->update->col = 0;
        $this->i = 0;
        $this->update->v = 0;
    }

    public function testUpdate():TicTacToe{
        $game = new TicTacToe();
        $this->update->i = 1;
        $this->update->v = 2;
        $this->assertTrue($game->update($this->update));
        $this->update->i = -1;
        $this->update->v = 2;
        $this->assertFalse($game->update($this->update));
        $this->update->i = 10;
        $this->update->v = 2;
        $this->assertFalse($game->update($this->update));
        $this->update->i = 1;
        $this->update->v = 1;
        $this->assertFalse($game->update($this->update));
        $this->update->i = 1;
        $this->update->v = 1;
        $this->assertFalse($game->update($this->update));
        $this->update->i = 1;
        $this->update->v = 0;
        $this->assertFalse($game->update($this->update));
        $this->assertSame(2,$game->getBoard()[1]);
        $this->update->i = 0;
        $this->update->v = 1;
        $this->assertTrue($game->update($this->update));
        $this->assertSame(1,$game->getBoard()[0]);
        return $game;
    }

    /*
    there is most likely a better way to do this 
    but since i don't have much experience in 
    unit testing its the best i got, I KNOW this method is bad 
    it dose not 100 determine the win alg works
    */
    public function testWin(){
        $wins = [[0,1,2], //first row
                 [0,3,6], //first column
                 [1,4,7], //second column
                 [3,4,5], //second row
                 [2,5,8], //third col
                 [6,7,8], //third row
                 [0,4,8], //across 1
                 [6,4,2], //across 2
                ];
        //foreach win condition need to add a new point to see if 
        //it works
        $losses =[[0,2,3,4,7],
                 [0,2,3,5,7],
                 [6,8,3,4,1],
                 [6,8,3,5,1],
                 [0,2,6,7,5],
                 [2,8,7,1,3],
                ];

        foreach($wins as $indexes){
            $game = new TicTacToe();
            foreach ($indexes as $i) {
                $this->update->i = $i;
                $this->update->v = 2;
                $this->assertTrue($game->update($this->update));
            }
            $this->assertTrue($game->win(2));
        }

        foreach($losses as $indexes){
            $game = new TicTacToe();
            foreach ($indexes as $i) {
                $this->update->i = $i;
                $this->update->v = 2;
                $this->assertTrue($game->update($this->update));
            }
            $this->assertFalse($game->win(2));
        }
    }
  
}


?>
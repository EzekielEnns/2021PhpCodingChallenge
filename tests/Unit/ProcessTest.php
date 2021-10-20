<?php 
//https://stackoverflow.com/questions/37284963/websocket-testing-functional
namespace MyApp;
include_once __DIR__ . '/../../src/Process.php';
use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase{
    private static $process;
    private $msg;
    private static $testyBois;  //an array of testing objects
    static function setUpBeforeClass():void{
        ProcessTest::$process = new Process(2,'MyApp\TicTacToe');
        ProcessTest::$testyBois = 
        [
            'bob' => new \stdClass(),
            'bill' => new \stdClass(),
            'zeek' => new \stdClass(),
            'cloe' => new \stdClass(),
        ];
    }

    protected function setUp(): void{
        $this->msg = new \stdClass;
        $this->msg->name = '';
        $this->msg->action = new \stdClass;
        $this->msg->action->i = 0;
        $this->msg->action->v = 0;
    }

    public function testIsValidInput(){
        $this->assertFalse(ProcessTest::$process->isValidInput(''));
        $test = new \stdClass;
        $this->assertFalse(ProcessTest::$process->isValidInput($test));
        $test->name = '';
        $this->assertFalse(ProcessTest::$process->isValidInput($test));
        $test->action = [1,3,4,5,6];
        $this->assertTrue(ProcessTest::$process->isValidInput($test));
    }

    //onMessage
  
    //3rd player is in lobby
    //until 4th enters
    public function testOnMessageFirstPlayer(){
        //adding bob to a match
        $from = ProcessTest::$testyBois['bob'];
        $this->msg->name = 'bob';
        $this->msg->action->i = 4;
        $this->msg->action->v = 1;
        $result = ProcessTest::$process->onMessage($from,$this->msg);
        
        //check if in lobby
        $this->assertTrue(ProcessTest::$process->matches->inLobby($from));
        //check if return for lobby is valid
        $this->assertSame($from,$result['Clients'][0]);
        $this->assertSame(1,count($result['msg']->names));
        
        //check if update worked
        $matches = ProcessTest::$process->matches;
        $this->assertSame(1,$matches->get($from)['game']->getBoard()[4]);
    }

    public function testOnMessageSecondPlayer(){
        $from = ProcessTest::$testyBois['bob'];
        $from2 = ProcessTest::$testyBois['bill'];
        $this->msg->name = 'bill';
        $this->msg->action->i = 4;
        $this->msg->action->v = 2;
        $result = ProcessTest::$process->onMessage($from2,$this->msg);
        
        //check if in active
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from));
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from2));
        //check if return for lobby is valid
        //test if update worked in active
        $matches = ProcessTest::$process->matches;
        $this->assertSame($matches->get($from2)['game']->getBoard()[4],
                          $matches->get($from)['game']->getBoard()[4]);
        $this->assertSame(2,count($result['msg']->names));
        
    }
    //tests if process can make a lobby and active match
    public function testOnMessageThirdPlayer(){
        //adding bob to a match
        $from = ProcessTest::$testyBois['zeek'];
        $this->msg->name = 'zeek';
        $this->msg->action->i = 4;
        $this->msg->action->v = 1;
        $result = ProcessTest::$process->onMessage($from,$this->msg);
        
        //check if in lobby
        $this->assertTrue(ProcessTest::$process->matches->inLobby($from));
        //check if return for lobby is valid
        $this->assertSame($from,$result['Clients'][0]);
        $this->assertSame(1,count($result['msg']->names));
        
        //check if update worked
        $matches = ProcessTest::$process->matches;
        $this->assertSame(1,$matches->get($from)['game']->getBoard()[4]);
    }

    public function testOnMessageFourthPlayer(){
        $from = ProcessTest::$testyBois['zeek'];
        $from2 = ProcessTest::$testyBois['cloe'];
        $this->msg->name = 'cloe';
        $this->msg->action->i = 4;
        $this->msg->action->v = 2;
        $result = ProcessTest::$process->onMessage($from2,$this->msg);
        
        //check if in active
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from));
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from2));
        //check if return for lobby is valid
        //test if update worked in active
        $matches = ProcessTest::$process->matches;
        $this->assertSame($matches->get($from2)['game']->getBoard()[4],
                          $matches->get($from)['game']->getBoard()[4]);
        $this->assertSame(2,count($result['msg']->names));
    }
    
    public function testOnMessageWin(){
        $from = ProcessTest::$testyBois['zeek'];
        $from2 = ProcessTest::$testyBois['cloe'];
        $this->msg->name = 'zeek';
        $this->msg->action->i = 0;
        $this->msg->action->v = 1;
        $result = ProcessTest::$process->onMessage($from,$this->msg);
        $this->msg->name = 'cloe';
        $this->msg->action->i = 5;
        $this->msg->action->v = 2;
        $result = ProcessTest::$process->onMessage($from2,$this->msg);
        $this->msg->name = 'zeek';
        $this->msg->action->i = 0;
        $this->msg->action->v = 1;
        $result = ProcessTest::$process->onMessage($from,$this->msg);
        $this->msg->name = 'cloe';
        $this->msg->action->i = 3;
        $this->msg->action->v = 2;
        $result = ProcessTest::$process->onMessage($from2,$this->msg);

        //check that cloe won
        $this->assertTrue($result['msg']->won);
        //check that cloe and zeek are in the lobby
        $this->assertTrue(ProcessTest::$process->matches->inLobby($from));
        $this->assertTrue(ProcessTest::$process->matches->inLobby($from2));
        
    }

    //check back into lobby
    public function testOnMessageBackToGame(){
        $from = ProcessTest::$testyBois['zeek'];
        $from2 = ProcessTest::$testyBois['cloe'];
        $this->msg->name = 'zeek';
        $this->msg->action->i = 0;
        $this->msg->action->v = 1;
        $result = ProcessTest::$process->onMessage($from,$this->msg);
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from));
        $this->assertFalse(ProcessTest::$process->matches->inLobby($from2));
        
    } 
}
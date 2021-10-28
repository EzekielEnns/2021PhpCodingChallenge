<?php 
namespace MyApp;
use PHPUnit\Framework\TestCase;

//why oh why god dose php unit pass obj's by reffernce 

class MatchMakerTest extends TestCase{
    private static $bob;
    private static $bill;

    static function setUpBeforeClass():void{
        MatchMakerTest::$bob = new \stdClass;
        MatchMakerTest::$bill = new \stdClass;
    }

    //checking to see if game class can eaily be used 
    public function testGameInMatch(){
        $matches = new MatchMaker(2,'MyApp\TicTacToe');
        //$matches->add(new \stdClass);
        $this->assertTrue(true);
    }

    public function testAdd():MatchMaker{
        $matches = new MatchMaker(2,'stdClass');
        $matches->add(MatchMakerTest::$bob);
        $this->assertIsArray($matches->get(MatchMakerTest::$bob));
        $this->assertIsArray($matches->get(MatchMakerTest::$bob)['party']);
        $this->assertInstanceOf(\stdClass::class,$matches->get(MatchMakerTest::$bob)['game']);
        
        return $matches;
    }

    /**
     * @depends testAdd
     */
    public function testFindActiveAdd(MatchMaker $matches){
        //this should add bill and bob to party
        
        $matches->add(MatchMakerTest::$bill);
        
        $this->assertFalse($matches->inLobby(MatchMakerTest::$bill)||$matches->inLobby(MatchMakerTest::$bob));
        $this->assertSame($matches->get(MatchMakerTest::$bill)['party'],$matches->get(MatchMakerTest::$bob)['party']);
        return $matches;
    }
    


}
?>
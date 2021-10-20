<?php 
namespace MyApp;
include_once __DIR__ . '/../../src/Matches.php';
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
        $matches->add(new \stdClass);
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
        $this->assertSame($matches->get(MatchMakerTest::$bill),$matches->get(MatchMakerTest::$bob));
        $this->assertContains(MatchMakerTest::$bob,$matches->get(MatchMakerTest::$bill)['party']);
        $this->assertContains(MatchMakerTest::$bill,$matches->get(MatchMakerTest::$bob)['party']);
        return $matches;
    }
    
    /**
     * @depends testFindActiveAdd
     */
    public function testRemoveLobby(MatchMaker $matches){
        $tommy = new \stdClass;
        $matches->add($tommy);
        $matches->removeLobby($tommy);
        $this->assertNull($matches->get($tommy));
    }


    /**
     * @depends testFindActiveAdd
     */
    public function testEndTurn(MatchMaker $matches){
        $first = $matches->get(MatchMakerTest::$bill)['party'][0];
        //note the goal of this is to get the party reffernce
        $matches->endTurn(MatchMakerTest::$bill);  //it dosent matter if its bills turn or not
        $this->assertSame($first,end($matches->get(MatchMakerTest::$bob)['party']));

    }
    /**
     * @depends testFindActiveAdd
     */
    public function testGameRef(MatchMaker $matches){
        $matches->get(MatchMakerTest::$bill)['game']->test = 1;
        $this->assertSame(1,$matches->get(MatchMakerTest::$bob)['game']->test);
    }

    /**
     * @depends testRemoveActive
     */
    public function testInLobby(MatchMaker $matches){
        $this->assertTrue($matches->inLobby(MatchMakerTest::$bob));
    }

    /**
     * @depends testFindActiveAdd
     */
    public function testRemoveActive(MatchMaker $matches){
        $matches->removeActive(MatchMakerTest::$bill);
        $this->assertTrue($matches->inLobby(MatchMakerTest::$bob));
        $this->assertTrue($matches->inLobby(MatchMakerTest::$bill));
        return $matches;
    }


    /**
     * @depends testRemoveActive
     */
    public function testFindActive(MatchMaker $matches){
        $matches->findActive(MatchMakerTest::$bill);
        $this->assertSame($matches->get(MatchMakerTest::$bill)['game'],$matches->get(MatchMakerTest::$bob)['game']);
    }




}
?>
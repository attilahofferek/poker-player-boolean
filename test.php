<?php

require "player.php";

define("H", "hearts");
define("S", "spades");
define("C", "clubs");
define("D", "diamonds");

class PlayerTest extends PHPUnit_Framework_TestCase
{
  private $b;

  public function __construct() {
    $this->b = new GameStateBuilder();
  }

  public function testKeepPairs()
  {
    $this->assertBet(10000000, $this->b->m([H => "K", S => "K"]));
    $this->assertBet(10000000, $this->b->m([H => 10, S => 10]));
    $this->assertBet(10000000, $this->b->m([H => 2, S => 2]));
  }

  public function testFoldLow()
  {
    $this->assertBet(0, $this->b->m([H => 3, S => 4]));
    $this->assertBet(0, $this->b->m([H => 3, S => "K"]));
    $this->assertBet(0, $this->b->m([H => 10, S => 2]));
  }

  public function testAllinOnlyUnderFourPlayers()
  {
    $this->assertBet(10000000, $this->b->a(2)->m([H => 10, S => 10]));
    $this->assertBet(10000000, $this->b->a(3)->m([H => 10, S => 10]));
    $this->assertBet(0, $this->b->a(4)->m([H => 10, S => 10]));
    $this->assertBet(0, $this->b->a(5)->m([H => 10, S => 10]));
    $this->assertBet(20, $this->b->b(20)->a(4)->m([H => 10, S => 10]));
    $this->assertBet(0, $this->b->b(100)->a(5)->m([H => 10, S => 10]));
  }

  public function testPostFlop()
  {
    $this->assertBet(0, $this->b->c([H => 4, S => 3, D => 10, S => 5])->m([H => 8, S => 2]));
    $this->assertBet(10000000, $this->b->c([D => 4, S => 3, H => 2, S => 5])->m([D => 2, S => 2]));
  }

  private function assertBet($bet, $gameState) {
    $player = new Player();
    $this->assertEquals($bet, $player->betRequest2($gameState));
  }
}

/*
$b = new GameStateBuilder();
$s = $b->c([C => "J", S => "J", D => "K"])->a(3)->m([H => "K", S => "K"]);
var_dump($s);
var_dump($player->getRainmanRank($s));
*/
class GameStateBuilder {
  private $DEFAULT_GAME = [
    "in_action" => 0,
    "players" => [[
      "hole_cards" => [],
      "bet" => 0,
      "status" => "active"
    ], ["status" => "out"], ["status" => "out"], ["status" => "out"], ["status" => "out"]],
    "small_blind" => 10,
    "current_buy_in" => 0,
    "community_cards" => [],
    "minimum_raise" => 120
  ];
  private $gameState;

  public function __construct() {
    $this->gameState = $this->DEFAULT_GAME;
  }

  public function a($playerNum) {
    $i = 0;
    foreach ($this->gameState["players"] as &$player) {
      $player["status"] = $i < $playerNum ? "active" : "out";
      ++$i;
    }
    return $this;
  }

  public function c($cards) {
    $this->gameState["community_cards"] = $this->makeCards($cards);
    return $this;
  }

  public function b($buyIn) {
    $this->gameState["current_buy_in"] = $buyIn;
    return $this;
  }

  private function makeCards($cards) {
    $newCards = [];
    foreach ($cards as $suit => $rank) {
      array_push($newCards, ["suit" => $suit, "rank" => $rank]);
    }
    return $newCards;
  }

  public function m($cards) {
    $this->gameState["players"][$this->gameState["in_action"]]["hole_cards"] = $this->makeCards($cards);
    $tmp = $this->gameState;
    $this->gameState = $this->DEFAULT_GAME;
    return $tmp;
  }
}

?>

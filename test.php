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

/*
var_dump($player->betRequest2(makeGame(makePair("10"))));
var_dump($player->betRequest2(makeGame(makePair("K"))));
var_dump($player->betRequest2(makeGame(makeCards("2", "4"))));
var_dump($player->betRequest2(makeGame(makeCards("2", "10"))));
var_dump($player->betRequest2(makeGame(makeCards("10", "2"))));
var_dump($player->betRequest2(makeGame(makeCards("2", "4"), 100, 90)));
var_dump($player->betRequest2(makeGame(makeCards("2", "4"), 100, 80)));
var_dump($player->betRequest2(makeGame(makeCards("2", "4"), 100, 60)));
var_dump($player->betRequest2(makeGame(makeCards("2", "4"), 100, 60)));
var_dump($player->betRequest2(makeGame(makeCards("2", "10"), 100, 10, 2)));
var_dump($player->betRequest2(makeGame(makeCards("2", "10"), 100, 10, 3)));
var_dump($player->betRequest2(makeGame(makeCards("2", "10"), 100, 10, 4)));
*/

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
    "community_cards" => []
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

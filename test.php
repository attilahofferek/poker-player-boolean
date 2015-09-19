<?php

require "player.php";

$data = '{
    "tournament_id":"550d1d68cd7bd10003000003",

    "game_id":"550da1cb2d909006e90004b1",
    "round":0,
    "bet_index":0,
    "small_blind": 10,
    "current_buy_in": 320,
    "pot": 400,
    "minimum_raise": 240,
    "dealer": 1,
    "orbits": 7,
    "in_action": 1,
    "players": [
        {

            "id": 0,

            "name": "Albert",

            "status": "active",
            "version": "Default random player",
            "stack": 1010,
            "bet": 320
        },
        {
            "id": 1,
            "name": "Bob",
            "status": "active",
            "version": "Default random player",
            "stack": 1590,
            "bet": 80,
            "hole_cards": [
                {
                    "rank": "6",
                    "suit": "hearts"
                },
                {
                    "rank": "K",
                    "suit": "spades"
                }
            ]
        },
        {
            "id": 2,
            "name": "Chuck",
            "status": "out",
            "version": "Default random player",
            "stack": 0,
            "bet": 0
        }
    ],
    "community_cards": [
        {
            "rank": "4",
            "suit": "spades"
        },
        {
            "rank": "A",
            "suit": "hearts"
        },
        {
            "rank": "6",
            "suit": "clubs"
        }
    ]
}';

$player = new Player();
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

function makeCards($value1, $value2) {
  return [["suit" => "hearts", "rank" => $value1], ["suit" => "clubs", "rank" => $value2]];
}

function makePair($value) {
  return [["suit" => "hearts", "rank" => $value], ["suit" => "clubs", "rank" => $value]];
}

function makeGame($cards, $buyin = 100, $bet = 100, $playerNum = 3) {
  $players = [["hole_cards" => $cards, "bet" => $bet, "status" => "active"]];
  for($i = 0; $i < 4; ++$i) {
    $status = $i < $playerNum - 1? "active" : "out";
    array_push($players, ["hole_cards" => [], "bet" => 100, "status" => $status]);
  }
  return ["in_action" => 0, "players" => $players, "small_blind" => 10, "current_buy_in" => $buyin];
}

?>

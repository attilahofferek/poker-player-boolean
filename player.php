<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
        return 10000000;
    }

    public function showdown($game_state)
    {
    }

    public function myCards($gameState) {
      return $gameState["players"][$gameState["in_anction"]]["hole_cards"];
    }
}

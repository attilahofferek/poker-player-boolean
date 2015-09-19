<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
		$myCards = $this->myCards($game_state);
		$smallBlind = $game_state['small_blind'];
		$bigBlind = $smallBlind * 2;
		$moneyNeedsToCall = $game_state['current_buy_in'] - $game_state["players"][$game_state["in_action"]]['bet'];
		if (
				$myCards[0]['rank'] == $myCards[1]['rank'] or
				in_array($myCards[0]['rank'], array('10', 'J', 'Q', 'K', 'A')) or
				in_array($myCards[1]['rank'], array('10', 'J', 'Q', 'K', 'A'))
		) {
					return 10000000;
		}

		if ($moneyNeedsToCall <= $bigBlind) {
			return $moneyNeedsToCall;
		}
		
		return 0;
    }

    public function showdown($game_state)
    {
    }

    public function myCards($gameState) {
		
		
		return $gameState["players"][$gameState["in_action"]]["hole_cards"];
    }
}

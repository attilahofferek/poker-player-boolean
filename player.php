<?php

class Player {

	const VERSION = "Default PHP folding player";
	const RAINMAN_URL = 'http://rainman.leanpoker.org/rank';

	public function betRequest($game_state) {
		return 0; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		if (count($game_state["community_cards"]) < 3) {
      return $this->preFlop($game_state);
    } else {
      return $this->postFlop($game_state);
    }
	}

	public function betRequest2($game_state) {
    if (count($game_state["community_cards"]) < 3) {
      return $this->preFlop($game_state);
    } else {
      return $this->postFlop($game_state);
    }
  }

  public function preFlop($game_state) {
		$myCards = $this->myCards($game_state);
		$smallBlind = $game_state['small_blind'];
		$bigBlind = $smallBlind * 2;
		$playersCount = $this->countActivePlayers($game_state);
		$moneyNeedsToCall = $game_state['current_buy_in'] - $game_state["players"][$game_state["in_action"]]['bet'];

		if ($this->getHandClass($myCards) >= 3 && $playersCount <= 3) {
			return 10000000;
		}

		$isClassTwo = ($this->getHandClass($myCards) === 2);

		if ($isClassTwo && $this->getPotOdds($game_state, $moneyNeedsToCall) < (1 / 4)) {
			return $moneyNeedsToCall;
		}

		return 0;
	}

  public function postFlop($game_state) {
    $rank = $this->getRainmanRank($game_state);
		if ($rank >= 3) {
			return 10000000;
		}
		$moneyNeedsToCall = $game_state['current_buy_in'] - $game_state["players"][$game_state["in_action"]]['bet'];
		if ($rank < 1) {
			return 0;
		}
    return $moneyNeedsToCall + max($game_state["minimum_raise"], $rank * 200);
  }




	public function showdown($game_state) {

	}

	public function myCards($gameState) {


		return $gameState["players"][$gameState["in_action"]]["hole_cards"];
	}

	public function countActivePlayers($game_state) {
		return count(array_filter($game_state['players'], function($item) {
					return $item['status'] === 'active';
				}));
	}


	public function getHandClass($cards) {
		$cCards = array_map(function($card){
			return $this->convertCard($card['rank']);
		}, $cards);

		if ($cCards[0] == $cCards[1]) { // magas párok
			if ($cCards[0] >= 10)
				return 5;
		}

		if ($cCards[0] >= 12 && $cCards[1] >= 12) { // magas lapok
			return 4;
		}

		if ($cCards[0] == $cCards[1]) { // alacsony párok
			if ($cCards[0] < 10)
				return 3;
		}

		if ($cards[0]['suit'] == $cards[1]['suit'] && abs($cCards[0] - $cCards[1]) == 1) { // sorhoz
			return 2;
		}

		return 1;
	}

	public function convertCard($rank) {
		switch ($rank) {
			case "A":
				return 14;
			case "K":
				return 13;
			case "Q":
				return 12;
			case "J":
				return 11;
			default:
				return (int) $rank;
		}
	}

	public function getRainmanRank($gameState) {
		$myCards = $this->myCards($gameState);
		$communityCards = $gameState['community_cards'];
		$cards = json_encode(array_merge($myCards, $communityCards));
		$urlCards = urlencode($cards);
		$rankJson = file_get_contents(self::RAINMAN_URL . "?cards=" . $urlCards);
		$rank = json_decode($rankJson, true);

		if (!in_array($myCards[0], $rank["cards_used"]) and
				!in_array($myCards[1], $rank["cards_used"]))
		{
			return 0;
		}

		if (1 == $rank['rank']) { // pair
			$highest = max(array_map(function($card){
				return $this->convertCard($card['rank']);
			}, $communityCards));

			if ($this->convertCard($rank["value"]) != $highest) { // we are not having the
				return 0;
			}
		}

		return $rank['rank'];
	}

	public function getPotOdds($game_state, $neededToCall) {
		return $neededToCall / $game_state['pot'];
	}
}

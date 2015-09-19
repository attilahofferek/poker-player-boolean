<?php

class Player {

	const VERSION = "Default PHP folding player";
	const RAINMAN_URL = 'http://rainman.leanpoker.org/rank';

	public function betRequest($game_state) {
		$myCards = $this->myCards($game_state);
		$smallBlind = $game_state['small_blind'];
		$bigBlind = $smallBlind * 2;
		$playersCount = $this->countActivePlayers($game_state);
		$moneyNeedsToCall = $game_state['current_buy_in'] - $game_state["players"][$game_state["in_action"]]['bet'];
		if (
				(
				$myCards[0]['rank'] == $myCards[1]['rank'] or
				in_array($myCards[0]['rank'], array('10', 'J', 'Q', 'K', 'A')) or
				in_array($myCards[1]['rank'], array('10', 'J', 'Q', 'K', 'A'))
				) and (
				$playersCount <= 3
				)
		) {
			return 10000000;
		}

		if ($moneyNeedsToCall <= $bigBlind) {
			return $moneyNeedsToCall;
		}

		return 0;
	}

	public function betRequest2($game_state) {
		$myCards = $this->myCards($game_state);
		$smallBlind = $game_state['small_blind'];
		$bigBlind = $smallBlind * 2;
		$playersCount = $this->countActivePlayers($game_state);
		$moneyNeedsToCall = $game_state['current_buy_in'] - $game_state["players"][$game_state["in_action"]]['bet'];
		if (
				(
				$myCards[0]['rank'] == $myCards[1]['rank'] or
				in_array($myCards[0]['rank'], array('10', 'J', 'Q', 'K', 'A')) or
				in_array($myCards[1]['rank'], array('10', 'J', 'Q', 'K', 'A'))
				) and (
				$playersCount <= 3
				)
		) {
			return 10000000;
		}

		if ($moneyNeedsToCall <= $bigBlind) {
			return $moneyNeedsToCall;
		}

		return 0;
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

	public function getRainmanRank($myCards, $gameState) {
		$cards = json_encode(array_merge($myCards, $gameState['community_cards']));
		$urlCards = urlencode($cards);
		$rankJson = file_get_contents(self::RAINMAN_URL . "?cards=" . $urlCards);
		$rank = json_decode($rankJson);

		return $rank['rank'];
	}

}

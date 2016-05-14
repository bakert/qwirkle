<?php

class Game {
  const HAND_SIZE = 6;
  const MAX_ROUNDS_WITHOUT_SCORING = 10;

  private $scores;

  public function __construct(array $players, $tiles = null) {
    Assert::type($players, Player);
    $this->players = $players;
    $this->random = $tiles === null;
    $this->tiles = $tiles;
    if ($this->random) {
      shuffle($this->players);
    }
  }

  public function go() {
    list($event, $bag, $board, $this->scores) = [new Event(), new Bag($this->tiles), new Board(), new Scores($this->players)];
    foreach ($this->players as $player) {
      $hand = new Hand($bag->draw(self::HAND_SIZE));
      $player->setHand($hand);
      $event->draw($player, $hand->tiles());
    }
    $players = $this->players;
    $startingPlayer = $this->determineStartingPlayer($players);
    $players = $this->putStartingPlayerFirst($startingPlayer, $players);
    $event->gameStart($players);
    list($finished, $roundsWithoutScoring) = [false, 0];
    while (!$finished) {
      $roundScore = 0;
      foreach ($players as $player) {
        $event->startTurn($player);
        $move = $player->move($board, $bag->isEmpty());
        if ($move->changeTiles()) {
          $bag->discard($player->discard());
          $draw = $bag->draw(self::HAND_SIZE);
          $player->draw($draw);
          $event->changeTiles($player, $draw);
        }
        $player->play($move);
        $score = $board->apply($move);
        $this->scores->score($player, $score);
        $roundScore += $score;
        $event->move($player, $move, $score, $board, $this->scores);
        if ($move->length() > 0 && !$bag->isEmpty()) {
          $tiles = $bag->draw($move->length());
          $player->draw($tiles);
          $event->draw($player, $tiles);
        }
        foreach ($players as $player) {
          if ($player->hand->size() === 0) {
            $this->scores->score($player, 6);
            $event->playerHasFinished($player);
            $finished = true;
            break 2;
          }
        }
      }
      if ($roundScore === 0) {
        $roundsWithoutScoring += 1;
        if ($bag->isEmpty()
            || $roundsWithoutScoring >= Game::MAX_ROUNDS_WITHOUT_SCORING) {
          $event->noPlayerCanMove();
          $finished = true;
        }
      }
    }
    $event->gameEnd($scores);
  }

  public function scores() {
    return $this->scores;
  }

  private function determineStartingPlayer(array $players) {
    list($startingPlayer, $bestStartingMove) = [null, 0];
    foreach ($players as $player) {
      $startingMove = $player->move(new Board(), false)->length();
      if ($startingMove > $bestStartingMove) {
        $startingPlayer = $player;
        $bestStartingMove = $startingMove;
      }
    }
    return $startingPlayer;
  }

  private function putStartingPlayerFirst(Player $startingPlayer, array $players) {
    list($first, $second, $found) = [[], [], false];
    foreach ($players as $player) {
      if ($player === $startingPlayer) {
        $found = true;
      }
      if (!$found) {
        array_push($second, $player);
      } else {
        array_push($first, $player);
      }
    }
    return array_merge($first, $second);
  }
}

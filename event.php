<?php

class Event {
  public function draw(Player $player, array $tiles) {
    Assert::type($tiles, Tile);
    $hand = new Hand($tiles); 
    $this->p("{$player} draws {$hand}");
    if (count($tiles) < $player->hand()->size()) {
      $this->p(", hand is {$player->hand()}");
    }
    $this->l()->l();
  }

  public function move(Player $player, Move $move, $score, Board $board, Scores $scores) {
    if (count($move->placements()) > 0) {
      $this->l("{$player} plays {$move} for {$score}");
      $this->l()->p($board);
      $this->l()->p($scores);
    } else {
      $this->l("$player changes his letters. Hand is now {$player->hand()}");
    }
    $this->l();
  }

  public function startTurn(Player $player) {
    $this->l("{$player} to play with hand of {$player->hand()}");
  }

  public function __call($f, array $args) {
    $this->l("$f");
  }

  private function p($s) {
    echo $s;
    return $this;
  }

  private function l($s = '') {
    echo "$s\n";
    return $this;
  }
}

<?php

class Score {
  const QWIRKLE_BONUS = 6;
  const FINISHING_BONUS = 6;

  public function __construct(Player $player, $score) {
    $this->player = $player;
    $this->score = $score;
  }

  public function player() {
    return $this->player;
  }

  public function score() {
    return $this->score;
  }

  public function __toString() {
    return (string)$this->score();
  }
}

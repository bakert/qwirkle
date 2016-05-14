<?php

class Scores {
  private $scores = [];

  public function __construct(array $players) {
    Assert::type($players, Player);
    foreach ($players as $player) {
      $this->scores[] = new Score($player, 0);
    }
  }

  public function score(Player $player, $newScore) {
    foreach ($this->scores as &$score) {
      if ($score->player() === $player) {
        $score = new Score($player, $score->score() + $newScore);
      }
    }
  }

  public function scores() {
    return $this->scores;
  }

  public function __toString() {
    list($longestName, $longestScore) = [0, 0];
    foreach ($this->scores as $score) {
      $name = mb_strlen($score->player()->name());
      $score = mb_strlen($score->score());
      if ($name > $longestName) {
        $longestName = $name;
      }
      if ($score > $longestScore) {
        $longestScore = $score;
      }
    }
    $s = '';
    foreach ($this->scores as $score) {
      $s .= str_pad($score->player()->name(), $longestName + 1);
      $s .= str_pad($score->score(), $longestScore, ' ', STR_PAD_LEFT);
      $s .= "\n";
    }
    return $s;
  }
}

<?php

class Move {
  const STARTING_INDEX = 99;

  public function __construct($placements) {
    Assert::type($placements, 'Placement');
    $this->placements = $placements;
  }

  public function placements() {
    return $this->placements;
  }

  public function length() {
    return count($this->placements());
  }

  public function changeTiles() {
    return count($this->placements) === 0;
  }

  public function lines(Board $board) {
    $lines = [];
    foreach ($this->placements() as $placement) {
      $theseLines = $placement->lines($board);
      $lines = array_merge($lines, $theseLines);
    }
    return(array_unique($lines));
  }

  public function __toString() {
    $a = [];
    foreach ($this->placements() as $placement) {
      $a[] = "$placement";
    }
    return implode(', ', $a);
  }
}

<?php

class Move {
  const STARTING_INDEX = 99;

  public function __construct($placements) {
    Assert::type($placements, Placement);
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

  public function __toString() {
    $a = [];
    foreach ($this->placements() as $placement) {
      $a[] = "$placement";
    }
    return implode(', ', $a);
  }
}

class Placement {
  public function __construct(Point $point, Tile $tile) {
    $this->point = $point;
    $this->tile = $tile;
  }

  public function point() {
    return $this->point;
  }

  public function tile() {
    return $this->tile;
  }

  public function __toString() {
    return "{$this->tile()} {$this->point()}";
  }
}

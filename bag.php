<?php

class Bag {
  public function __construct() {
    $this->tiles = [];
    foreach (Color::colors() as $color) {
      foreach (Shape::shapes() as $shape) {
        $tile = new Tile($color, $shape);
        $this->tiles[] = $tile;
        $this->tiles[] = $tile;
        $this->tiles[] = $tile;
      }
    }
    $this->shuffle();
  }

  public function draw($n) {
    $tiles = [];
    for ($i = 0; $i < $n; $i++) {
      $tile = array_shift($this->tiles);
      if ($tile === null) {
        return $tiles;
      }
      $tiles[$i] = $tile;
    }
    return $tiles;
  }

  public function discard($tiles) {
    $this->tiles = array_merge($this->tiles, $tiles);
    $this->shuffle();
  }

  public function isEmpty() {
    return count($this->tiles) === 0;
  }

  private function shuffle() {
    if (Game::RANDOM) {
      shuffle($this->tiles);
    }
  }
}

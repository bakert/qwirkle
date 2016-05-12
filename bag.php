<?php

class Bag {
  public function __construct($tiles = null) {
    $this->random = $tiles === null;
    $this->tiles = $tiles === null ? Tile::allTiles() : $tiles;
    if ($this->random) {
      $this->shuffle();
    }
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
    if ($this->random) {
      $this->shuffle();
    }
  }

  public function isEmpty() {
    return count($this->tiles) === 0;
  }

  private function shuffle() {
    if ($this->random) {
      shuffle($this->tiles);
    }
  }
}

<?php

class Hand {
  public function __construct(array $tiles = []) {
    Assert::type($tiles, Tile);
    $this->tiles = $tiles;
  }

  public function tiles() {
    return $this->tiles;
  }

  public function size() {
    return count($this->tiles);
  }

  public function play(Move $move) {
    foreach ($move->placements() as $placement) {
      $pos = array_search($placement->tile(), $this->tiles);
      if ($pos === false) {
        throw new IllegalMoveException("Could not find tile {$placement->tile()} in {$this}");
      }
      unset($this->tiles[$pos]);
    }
  }

  public function draw(array $tiles) {
    $this->tiles = array_merge($this->tiles, $tiles);
  }

  public function withProperty($property) {
    $matches = [];
    foreach ($this->tiles as $tile) {
      if ($tile->color()->name() === $property || $tile->shape()->name === $property) {
        $matches[] = $tile;
      }
    }
    return $matches;
  }

  public function __toString() {
    $a = [];
    foreach ($this->tiles() as $tile) {
      $a[] = "$tile";
    }
    return implode(", ", $a);
  }
}

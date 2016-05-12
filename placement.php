<?php

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

<?php

class Line {
  public function __construct(array $placements = []) {
    Assert::type($placements, Placement);
    $this->placements = $placements;
  }

  public function placements() {
    return $this->placements;
  }

  public function tiles() {
    $tiles = [];
    foreach ($this->placements as $placement) {
      $tiles[] = $placement->tile();
    }
    return $tiles;
  }

  public function isLegal() {
    if (!$this->placements()) {
      return true;
    }
    if ($this->tiles() !== array_unique($this->tiles())) {
      return false;
    }
    $color = $this->tiles()[0]->color();
    $shape = $this->tiles()[0]->shape();
    foreach ($this->tiles() as $tile) {
      if ($tile->color() !== $color) {
        $color = null;
      }
      if ($tile->shape() !== $shape) {
        $shape = null;
      }
    }
    return $color !== null || $shape !== null;
  }

  public function length() {
    return count($this->placements);
  }

  public function contains($searchTile) {
    foreach ($this->tiles() as $tile) {
      if ($tile === $searchTile) {
        return true;
      }
    }
    return false;
  }

  public function sharedProperty() {
    $sharedProperty = null;
    foreach (Color::colors() as $color) {
      foreach ($this->tiles() as $tile) {
        if ($sharedProperty === null || $sharedProperty === $tile->color()) {
          $sharedProperty = $tile->color();
        } else {
          $sharedProperty = null;
          break 2;
        }
      }
    }
    foreach (Shape::shapes() as $shape) {
      foreach ($this->tiles() as $tile) {
        if ($sharedProperty === null || $sharedProperty === $tile->shape()) {
          $sharedProperty = $tile->shape();
        } else {
          return null;
        }
      }
    }
    return $sharedProperty;
  }

  public function __toString() {
    $a = [];
    foreach ($this->placements as $placement) {
      $a[] = "$placement";
    }
    return "Line: " . implode(', ', $a);
  }
}
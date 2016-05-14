<?php

trait Tiles {
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
}
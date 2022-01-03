<?php

trait Tiles {
  public function sharedProperty() {
    $sharedProperties = $this->sharedProperties();
    if (count($sharedProperties) === 0) {
      return null;
    } elseif (count($sharedProperties) === 1) {
      return $sharedProperties[0];
    } else {
      throw new IllegalArgumentException("Cannot called sharedProperty on a single tile or duplicates of a single tile.");
    }
  }

  public function sharedProperties() {
    $sharedProperty = null;
    $sharedProperties = [];
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
    if ($sharedProperty !== null) {
      $sharedProperties[] = $sharedProperty;
    }
    $sharedProperty = null;
    foreach (Shape::shapes() as $shape) {
      foreach ($this->tiles() as $tile) {
        if ($sharedProperty === null || $sharedProperty === $tile->shape()) {
          $sharedProperty = $tile->shape();
        } else {
          $sharedProperty = null;
          break 2;
        }
      }
    }
    if ($sharedProperty !== null) {
      $sharedProperties[] = $sharedProperty;
    }
    return $sharedProperties;
  }
}
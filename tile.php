<?php

class Tile {
  const USE_ANSI = true;

  private static $allTiles;

  public static function allTiles() {
    if (!self::$allTiles) {
      self::$allTiles = [];
      foreach (Color::colors() as $color) {
        foreach (Shape::shapes() as $shape) {
          $tile = new Tile($color, $shape);
          self::$allTiles[] = $tile;
          self::$allTiles[] = $tile;
          self::$allTiles[] = $tile;
        }
      }
    }
    return self::$allTiles;
  }

  public static function get(Color $color, Shape $shape) {
    foreach (self::allTiles() as $tile) {
      if ($tile->color() === $color && $tile->shape() === $shape) {
        return $tile;
      }
    }
  }

  public function __construct(Color $color, Shape $shape) {
    $this->color = $color;
    $this->shape = $shape;
  }

  public function color() {
    return $this->color;
  }

  public function shape() {
    return $this->shape;
  }

  public function __toString() {
    if (self::USE_ANSI) {
      $colorString = Color::ANSI_BACKGROUND . $this->color()->ansi();
      $closeColorString = "\e[0m" /* turn off previous escape codes */;
    } else {
      $colorString = "{$this->color()}";
      $closeColorString = '';
    }
    return "{$colorString}{$this->shape()}{$closeColorString}";
  }
}

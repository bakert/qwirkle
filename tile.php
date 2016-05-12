<?php

class Tile {
  const USE_ANSI = true;

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

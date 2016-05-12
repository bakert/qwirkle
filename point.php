<?php

class Point {
  public function __construct($x, $y) {
    $this->x = $x;
    $this->y = $y;
  }

  public function x() {
    return $this->x;
  }

  public function y() {
    return $this->y;
  }

  public function next(Direction $direction) {
    $x = $this->x;
    $y = $this->y;
    if ($direction === Direction::up()) {
      $y += 1;
    } elseif ($direction === Direction::down()) {
      $y -= 1;
    } elseif ($direction === Direction::right()) {
      $x += 1;
    } elseif ($direction === Direction::left()) {
      $x -= 1;
    } else {
      throw new IllegalDirectionException($direction);
    }
    return new Point($x, $y);
  }

  public function __toString() {
    return "({$this->x()}, {$this->y()})";
  }
}

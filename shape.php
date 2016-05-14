<?php

class Shape {
  private static $circle;
  private static $clover;
  private static $diamond;
  private static $square;
  private static $star;
  private static $x;

  private static $representations = ['circle' => 'o', 'clover' => '+', 'diamond' => 'v', 'square' => '#', 'star' => '*', 'x' => 'x'];

  public static function shapes() {
    return [self::circle(), self::clover(), self::diamond(), self::square(), self::star(), self::x()];
  }

  public static function __callStatic($f, array $args) {
    if (!self::$$f) {
      self::$$f = new Shape($f, self::$representations[$f]);
    }
    return self::$$f;
  }

  private function __construct($name, $representation) {
    $this->name = $name;
    $this->representation = $representation;
  }

  public function name() {
    return $this->name;
  }

  public function representation() {
    return $this->representation;
  }

  public function __toString() {
    return $this->representation();
  }
}
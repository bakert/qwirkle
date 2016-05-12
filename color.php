<?php

class Color {
  const ANSI_BACKGROUND = "\e[47m";

  private static $blue;
  private static $green;
  private static $lilac;
  private static $orange;
  private static $red;
  private static $yellow;

  private static $ansi = [
    'blue' => "\e[34m", 
    'green' => "\e[1m", // 0 is bold black, not green but green wasn't very distinguishable.
    'lilac' => "\e[35m", 
    'orange' => "\e[36m", // 36 is actually cyan but \e[48;2;255;160m is an (even more) unreliable orange.
    'red' => "\e[31m", 
    'yellow' => "\e[33m"
  ];

  public static function colors() {
    return [self::blue(), self::green(), self::lilac(), self::orange(), self::red(), self::yellow()];
  }

  public static function __callStatic($f, array $args) {
    if (!self::$$f) {
      self::$$f = new Color($f);
    }
    return self::$$f;
  }

  private function __construct($name) {
    $this->name = $name;
  }

  public function name() {
    return $this->name;
  }

  public function ansi() {
    return self::$ansi[$this->name()];
  }

  public function __toString() {
    return mb_strtoupper(mb_substr($this->name(), 0, 1));
  }
}

<?php

class Direction {
  private static $initialized = false;
  private static $left;
  private static $right;
  private static $up;
  private static $down;

  private static function initialized() {
    return self::$intialized();
  }

  private static function initialize() {
    self::$up = new Direction('up');
    self::$down = new Direction('down');
    self::$left = new Direction('left');
    self::$right = new Direction('right');
    self::$initialized = true;
  }

  public static function up() {
    if (!self::$initialized) {
      self::initialize();
    }
    return self::$up;
  }

  public static function down() {
    if (!self::$initialized) {
      self::initialize();
    }
    return self::$down;
  }

  public static function left() {
    if (!self::$initialized) {
      self::initialize();
    }
    return self::$left;
  }

  public static function right() {
    if (!self::$initialized) {
      self::initialize();
    }
    return self::$right;
  }

  private $name;

  private function __construct($name) {
    $this->name = $name;
  }

  public function opposite() {
    if ($this->name() === Direction::up()->name()) {
      return Direction::down();
    } elseif ($this === Direction::down()) {
      return Direction::up();
    } elseif ($this === Direction::left()) {
      return Direction::right();
    } elseif ($this === Direction::right()) {
      return Direction::left();
    }
    throw new IllegalDirectionException((string)$this);
  }

  public function name() {
    return $this->name;
  }

  public function __toString() {
    return $this->name();
  }
}

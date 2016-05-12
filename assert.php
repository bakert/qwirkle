<?php

class Assert {
  public static function type(array $a, $class) {
    foreach ($a as $item) {
      if (get_class($item) !== $class && !is_subclass_of($item, $class)) {
        throw new TypeException("$item is not of type $class");
      }
    }
  }
}

<?php

class Autoloader {
  public function load($name) {
    foreach (['', 'exceptions/'] as $dir) {
      $path = __DIR__ . '/' . $dir . mb_strtolower($name) . '.php';
      if (file_exists($path)) {
        require_once $path;
      }
    }
  }
}


<?php

class Location {
  public function __construct(Point $point, array $sharedProperties) {
    $this->point = $point;
    $this->sharedProperties = $sharedProperties;
  }

  public function point() {
    return $this->point;
  }

  public function sharedProperties() {
    return $this->sharedProperties;
  }
}

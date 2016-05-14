<?php

class Placement {
  public function __construct(Point $point, Tile $tile) {
    $this->point = $point;
    $this->tile = $tile;
  }

  public function point() {
    return $this->point;
  }

  public function tile() {
    return $this->tile;
  }

  public function isLegal(Board $board) {
    $lines = $this->lines($board);
    foreach ($lines as $line) {
      if (!$line->isLegal()) {
        return false;
      }
    }
    return true;
  }

  public function lines(Board $board) {
    list($x, $y) = [$this->point()->x(), $this->point()->y()];
    $lines = [];
    $lines[] = new Line($this->getHorizontalLine($board, $x, $y));
    $lines[] = new Line($this->getVerticalLine($board, $x, $y));
    return $lines;
  }

  private function getHorizontalLine(Board $board, $x, $y) {
    return array_merge(array_reverse($this->getPartialLine($board, $x, $y, -1, 0)), $this->getPartialLine($board, $x - 1, $y, 1, 0));
  }

  private function getVerticalline(Board $board, $x, $y) {
    return array_merge(array_reverse($this->getPartialLine($board, $x, $y, 0, -1)), $this->getPartialLine($board, $x, $y - 1, 0, 1));
  }

  private function getPartialLine(Board $board, $x, $y, $xMod, $yMod) {
    $line = [];
    while (true) {
      $x += $xMod;
      $y += $yMod;
      $point = new Point($x, $y);
      if ($board->at($point) === null) {
        break;
      }
      $line[] = new Placement($point, $board->at($point));
    }
    return $line;
  }

  public function __toString() {
    return "{$this->tile()} {$this->point()}";
  }
}

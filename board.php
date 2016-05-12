<?php

class Board {
  const SIZE = 200;
  const MIN_VIEWPORT_SIZE = 0;

  private $board;

  public function __construct(Board $board = null) {
    if (!$board) {
      $this->board = array_fill(0, self::SIZE, []);
      for ($y = 0; $y <= self::SIZE; $y++) {
        $this->board[$y] = array_fill(0, self::SIZE, null);
      }
    } else {
      $this->board = $board;
    }
  }

  public function isLegal(Move $move) {
    foreach ($move->placements() as $placement) {
      if ($this->spotTaken($placement->point())) {
        return false;
      }
    }
    $board = clone $this;
    $board->applyWithoutChecks($move);
    foreach ($move->placements() as $placement) {
      if (!$this->isLegalPlacement($placement, $board)) {
        return false;
      }
    }
    return true;
  }

  private function isLegalPlacement(Placement $placement, Board $board) {
    $linesFromPlacement = $this->linesFromPlacement($placement, $board);
    foreach ($linesFromPlacement as $line) {
      if (!$line->isLegal($line)) {
        return false;
      }
    }
    return true;
  }

  private function linesFromPlacement(Placement $placement, Board $board) {
    list($x, $y) = [$placement->point()->x(), $placement->point()->y()];
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

  public function apply(Move $move) {
    if (!$this->isLegal($move)) {
      throw new IllegalMoveException("Cannot place move `{$move}` on board `{$this}`");
    }
    $this->applyWithoutChecks($move);
    return $this->score($move);
  }

  private function applyWithoutChecks(Move $move) {
    foreach ($move->placements as $placement) {
      list($x, $y) = [$placement->point()->x(), $placement->point()->y()];
      $this->board[$y][$x] = $placement->tile();
    }
    return $board;
  }

  public function at(Point $point) {
    if ($point->x() < 0 || $point->y() < 0 || $point->x() >= self::SIZE || $point->y() >= self::SIZE) {
      return null;
    }
    return $this->board[$point->y()][$point->x()];
  }

  public function spotTaken(Point $point) {
    return isset($this->board[$point->y()][$point->x()]);
  }

  public function attachmentLocations() {
    $locations = [];
    for ($y = 0; $y < count($this->board); $y++) {
      for ($x = 0; $x < count($this->board[$y]); $x++) {
        $point = new Point($x, $y);
        if (!$this->spotTaken($point) && $this->adjacentIsOccupied($point)) {
          $locations[] = $point;
        }
      }
    }
    return $locations;
  }

  private function adjacentIsOccupied(Point $point) {
    return isset($this->board[$point->y() - 1][$point->x()]) 
      || isset($this->board[$point->y() + 1][$point->x()]) 
      || isset($this->board[$point->y()][$point->x() - 1]) 
      || isset($this->board[$point->y()][$point->x() + 1]);
  }

  public function isEmpty() {
    foreach ($this->board as $column) {
      foreach ($column as $tile) {
        if ($tile) {
          return false;
        }
      }
    }
    return true;
  }

  public function score(Move $move) {
    $board = clone $this;
    $board->applyWithoutChecks($move);
    $lines = [];
    foreach ($move->placements() as $placement) {
      $linesFromPlacement = $board->linesFromPlacement($placement, $board);
      $lines = array_merge($lines, $linesFromPlacement);
    }
    $lines = array_unique($lines);
    $points = 0;
    foreach ($lines as $line) {
      if ($line->length() === count(Color::colors())) {
        $score += Score::QWIRKLE_BONUS;
      }
      if ($line->length() > 1) {
        $score += $line->length();
      }
    }
    return $score;
  }

  public function __toString() {
    $highest = self::SIZE / 2 - self::MIN_VIEWPORT_SIZE / 2 - 1;
    $lowest = self::SIZE / 2 + self::MIN_VIEWPORT_SIZE / 2 - 1;
    $leftmost = self::SIZE / 2 - self::MIN_VIEWPORT_SIZE / 2 - 1;
    $rightmost = self::SIZE / 2 + self::MIN_VIEWPORT_SIZE / 2 - 1;

    for ($y = 0; $y < count($this->board); $y++) {
      for ($x = 0; $x < count($this->board[$y]); $x++) {
        if ($this->board[$y][$x] !== null) {
          $highest = min($y, $highest);
          $lowest = max($y, $lowest);
          $leftmost = min($x, $leftmost);
          $rightmost = max($x, $rightmost);
        }
      }
    }
    $highest -= 1;
    $lowest += 2;
    $leftmost -= 1;
    $rightmost += 2;

    for ($y = $highest; $y < $lowest; $y++) {
      $a = [];
      for ($x = $leftmost; $x < $rightmost; $x++) {
        $tile = $this->board[$y][$x];
        if ($tile === null) {
          $tile = Tile::USE_ANSI ? Color::ANSI_BACKGROUND . " \e[0m" : '  ';
        }
        $a[] = "{$tile}";
      }
      $s .= '|' . implode("|", $a) . "|\n";
    }
    return $s;
  }
}
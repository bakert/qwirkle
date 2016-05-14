<?php

class Board {
  const SIZE = 200;
  const MIN_VIEWPORT_SIZE = 0;

  private $board;
  private $locations = [];

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
      if (!$placement->isLegal($board)) {
        return false;
      }
    }
    return true;
  }

  public function apply(Move $move) {
    if (!$this->isLegal($move)) {
      throw new IllegalMoveException("Cannot place move `{$move}` on board `{$this}`");
    }
    $this->applyWithoutChecks($move, true);
    return $this->score($move);
  }

  private function applyWithoutChecks(Move $move, $updateLocations = false) {
    foreach ($move->placements as $placement) {
      $this->removeLocation($placement->point());
      list($x, $y) = [$placement->point()->x(), $placement->point()->y()];
      $this->board[$y][$x] = $placement->tile();
    }
    if ($updateLocations) {
      foreach ($move->placements as $placement) {
        foreach ($placement->point()->neighbors() as $point) {
          if ($this->at($point) === null) {
            $this->addLocation($point);
          }
        }
      }
    }
    return $board;
  }

  private function addLocation(Point $pointToAdd) {
    foreach ($pointToAdd->neighbors() as $point) {
      $tile = $this->at($point);
      if ($tile !== null) {
        $neighbors[] = $tile;
      }
    }
    $sharedProperties = (new Hand($neighbors))->sharedProperties();
    if ($sharedProperties !== null) {
      $this->locations[(string)$pointToAdd] = ['point' => $pointToAdd, 'sharedProperties' => $sharedProperties];
    }
  }

  private function removeLocation(Point $point) {
    unset($this->locations[(string)$point]);
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
    $locations = array_values($this->locations);
    return $locations;
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
    $lines = $move->lines($board);
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

  public function tiles() {
    $tiles = [];
    foreach ($this->board as $column) {
      foreach ($column as $tile) {
        $tiles[] = $tile;
      }
    }
    return $tiles;
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
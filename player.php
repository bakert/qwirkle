<?php

class Player {
  public function __construct($name) {
    $this->name = $name;
    $this->hand = new Hand();
  }

  public function name() {
    return $this->name;
  }

  public function hand() {
    return $this->hand;
  }

  public function setHand(Hand $hand) {
    $this->hand = $hand;
  }

  public function draw(array $tiles) {
    $this->hand->draw($tiles);
  }

  public function discard() {
    $tiles = $this->hand()->tiles();
    $this->hand = new Hand();
    return $tiles;
  }

  public function startingMove(array $tiles) {
    Assert::type($tiles, Tile);
    $lines = [];
    foreach ($tiles as $tile) {
      if (!isset($lines[$tile->color()->name()])) {
        $lines[$tile->color()->name()] = 0;
      }
      $lines[$tile->color()->name()] += 1;
      if (!isset($lines[$tile->shape()->name()])) {
        $lines[$tile->shape()->name()] = 0;
      }
      $lines[$tile->shape()->name()] += 1;
    }
    list($best, $max) = [null, 0];
    foreach ($lines as $key => $count) {
      if ($count > $max) {
        $best = $key;
        $max = $count;
      }
    }
    list($x, $placements) = [Move::STARTING_INDEX, []];
    foreach ((new Hand($tiles))->withProperty($best) as $tile) {
      $placements[] = new Placement(new Point($x, Move::STARTING_INDEX), $tile);
      $x += 1;
    }
    return new Move($placements);
  }

  public function play(Move $move) {
    $this->hand()->play($move);
  }

  public function move(Board $board, $bagIsEmpty) {
    $tiles = array_unique($this->hand()->tiles());
    if ($board->isEmpty()) {
      return $this->startingMove($tiles);
    }
    $usefulSets = $this->usefulSets($tiles);
    list($max, $bestMove) = [0, new Move([])];
    foreach ($usefulSets as $usefulSet) {
      $move = $this->bestMoveWith($usefulSet, $board);
      if ($move) {
        $score = $board->score($move);
        if (count($move->placements()) === count($tiles) && $bagIsEmpty) {
          $score += Score::FINISHING_BONUS;
        }
        if ($score > $max) {
          $max = $score;
          $bestMove = $move;
        }
      }
    }
    return $bestMove;
  }

  private function usefulSets(array $tiles) {
    Assert::type($tiles, Tile);
    if (array_unique($tiles) !== $tiles) {
      throw new QwirkleException("fuck");
    }
    $usefulSets = [];
    foreach (array_merge(Color::colors(), Shape::shapes()) as $property) {
      $propertySet = (new Hand($tiles))->withProperty($property->name());
      if ($propertySet) {
        foreach ($this->powerset($propertySet) as $set) {
          if (count($set) > 0) {
            $usefulSets[] = $set;
          }
        }
      }
    }
    return $usefulSets;
  }

  private function powerset(array $items) {
    $results = [[]];
    foreach ($items as $item) {
      foreach ($results as $combination) {
        $results[] = array_merge([$item], $combination);
      }
    }
    return $results;
  }

  private function bestMoveWith(array $tiles, Board $board) {
    list($max, $bestMove) = [0, new Move([])];
    foreach ($board->attachmentLocations() as $point) {
      foreach ($this->permutations($tiles, $board, $point) as $move) {
        if ($board->isLegal($move)) {
          $score = $board->score($move);
          if ($score > $max) {
            $max = $score;
            $bestMove = $move;
          }
        }
      }
    }
    return $bestMove;
  }

  private function permutations(array $tiles, Board $board, Point $point) {
    $moves = [];
    foreach ([Direction::down(), Direction::right()] as $direction) {
      if (count($tiles) === 1 && $direction === Direction::right()) {
        continue; // No point in checking single tiles more than once in each spot.
      }
      for ($i = 0; $i < count($tiles); $i++) {
        list($steps, $p) = [$i, $point];
        while ($steps > 0) {
          $p = $p->next($direction->opposite());
          if ($board->at($p) === null) {
            $steps -= 1;
          }
        }
        foreach ($this->orderings($tiles) as $tilesToLay) {
          $layingP = $p;
          $tilesHand = new Hand($tilesToLay);
          $placements = [];
          while ($tilesToLay) {
            if ($board->at($layingP) === null) {
              $placements[] = new Placement($layingP, array_shift($tilesToLay));
            }
            $layingP = $layingP->next($direction);
          }
          $moves[] = new Move($placements);
        }
      }
    }
    return $moves;
  }

  private function orderings(array $items, array $processed = []) {
    $result = [];
    foreach ($items as $key => $value) {
      $copy = $processed;
      $copy[$key] = $value;
      $tmp = array_diff_key($items, $copy);
      if (count($tmp) === 0) {
        $result[] = $copy;
      } else {
        $result = array_merge($result, $this->orderings($tmp, $copy));
      }
    }
    return $result;
  }

  public function __toString() {
    return $this->name();
  }
}

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
    return $this->chooseMove(new Hand($tiles), $board, $bagIsEmpty);
  }

  protected function chooseMove(Hand $hand, Board $board, $bagIsEmpty) {
    $usefulSets = $this->usefulSets($hand);
    list($max, $bestMove) = [0, new Move([])];
    foreach ($usefulSets as $usefulSet) {
      $move = $this->bestMoveWith($usefulSet, $board);
      if ($move) {
        $score = $this->evaluate($hand, $move, $board, $bagIsEmpty);
        if ($score > $max) {
          $max = $score;
          $bestMove = $move;
        }
      }
    }
    return $bestMove;
  }

  protected function evaluate(Hand $hand, Move $move, Board $board, $bagIsEmpty) {
    $score = $board->score($move);
    //BAKERT bug here ... we have already removed the non unique tiles
    if (count($move->placements()) === $hand->size() && $bagIsEmpty) {
      $score += Score::FINISHING_BONUS;
    }
    return $score;
  }

  private function usefulSets(Hand $hand) {
    $usefulSets = [];
    foreach (array_merge(Color::colors(), Shape::shapes()) as $property) {
      $propertySet = $hand->withProperty($property->name());
      if ($propertySet) {
        foreach ($this->powerset($propertySet) as $set) {
          if (count($set) > 0) {
            $usefulSets[] = new Hand($set);
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

  private function bestMoveWith(Hand $hand, Board $board) {
    list($max, $bestMove) = [0, new Move([])];
    foreach ($board->attachmentLocations() as $location) {
      //BAKERT shoulld location be a class and does that add much overhead?
      // Skip if a quick look convinces us the move will be illegal. For speed.
      // Shaves about 20s off a 48s game.
      if (!$this->quickCheckLocation($location, $hand)) {
        continue;
      }
      foreach ($this->permutations($hand, $board, $location->point()) as $move) {
        $score = $board->score($move);
        if ($score > $max) {
          $max = $score;
          $bestMove = $move;
        }
      }
    }
    return $bestMove;
  }

  private function quickCheckLocation(Location $location, Hand $hand) {
    foreach ($hand->sharedProperties() as $property) {
      if (in_array($property, $location->sharedProperties())) {
        return true;
      }
    }
    return false;
  }

  private function permutations(Hand $hand, Board $board, Point $point) {
    $moves = [];
    foreach ([Direction::down(), Direction::right()] as $direction) {
      if ($hand->size() === 1 && $direction === Direction::right()) {
        continue; // No point in checking single tiles more than once in each spot.
      }
      for ($i = 0; $i < $hand->size(); $i++) {
        list($steps, $p) = [$i, $point];
        while ($steps > 0) {
          $p = $p->next($direction->opposite());
          if ($board->at($p) === null) {
            $steps -= 1;
          }
        }
        //BAKERT perf improvement - don't try illegal tiles in the initial slot
        //BAKERT at some point in here we know every tile and every point we are going to lay
        // but we will do things like try a set of 4 xs in every possible permutation against a circle

        //BAKERT experimental dual mode to reduce worst case but keep faster easy case
        if ($hand->size() >= 3 /* make this a constant BAKERT */) {
          $move = $this->tryFit($hand->tiles(), $board, $p, $direction);
          if ($move !== null) {
            $moves[] = $move;
          }
        } else {
          foreach ($this->orderings($hand->tiles()) as $tilesToLay) {
            $layingP = $p;
            $placements = [];
            while ($tilesToLay) {
              if ($board->at($layingP) === null) {
                $placements[] = new Placement($layingP, array_shift($tilesToLay));
              }
              $layingP = $layingP->next($direction);
            }
            $move = new Move($placements);
            //BAKERT
            // if (Game::$turn === 51) {
            //   $board2 = clone $board;
            //   $board2->applyWithoutChecks($move);
            //   echo $board2;
            // }
            if ($board->isLegal($move)) {
              $moves[] = $move;
              break; // We found a legal move using these tiles in this direction from this start position - we cannot do better score-wise.
            }
          }
        }
      }
    }
    return $moves;
  }

  private function tryFit(array $tiles, Board $board, Point $point, Direction $direction, array $rawMove = []) {
    if (count($tiles) === 0) {
      return new Move($rawMove);
    }
    foreach ($tiles as $tile) {
      $placement = new Placement($point, $tile);
      $newRawMove = array_merge($rawMove, [$placement]);
      $move = new Move($newRawMove);
      //BAKERT experiment with these numbers
      if ((count($rawMove) <= 2 && count($tiles) > 1) || $board->isLegal($move)) {
        $pos = array_search($tile, $tiles);
        unset($tiles[$pos]);
        $board = clone $board;
        $board->applyWithoutChecks($move);
        $move = $this->tryFit($tiles, $board, $point->next($direction), $direction, $newRawMove);
        if ($move !== null) {
          return $move;
        }
      }
    }
    return null;
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

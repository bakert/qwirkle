<?php

class CautiousPlayer extends Player {
  public function evaluate(Hand $hand, Move $move, Board $board, $bagIsEmpty) {
    $score = $board->score($move);
    if (count($move->placements()) === $this->hand()->size() && $bagIsEmpty) {
      $score += Score::FINISHING_BONUS;
    } elseif ($this->enablesQwirkle($move, $board) && $score < 9) {
      $score -= 3.5; //BAKERT this modifier should be based on the number of players
    } elseif ($this->enablesDoubleQwirkle($move, $board) && $score < 9) {
      $score -= 0.5;
    }
    return $score;
  }

  // BAKERT combine these two enables functions can be shared
  // BAKERT we don't actually look if the qwirkle is playable and that's bad ... maybe the squares would not be legal.
  private function enablesQwirkle(Move $move, Board $board) {
    foreach ($move->lines($board) as $line) {
      if ($line->length() === count(Color::colors()) - 1) {
        $missingPiece = $this->missingPieces($line)[0];
        if ($this->notAccountedFor($missingPiece, $board)) {
          return true;
        }
      }
    }
    return false;
  }

  private function enablesDoubleQwirkle(Move $move, Board $board) {
    foreach ($move->lines($board) as $line) {
      if ($line->length() === count(Color::colors()) - 2) {
        $missingPieces = $this->missingPieces($line);
        if ($this->notAccountedFor($missingPiece[0], $board)
          && $this->notAccountedFor($missingPiece[1], $board)) {
          return true;
        }
      }
    }
    return false;
  }

  private function missingPieces($line) {
    if ($line->length() <= 1) {
      throw new IllegalArgumentException("Qwirkle this lines is part of is ambiguous ($line).");
    }
    $sharedProperty = $line->sharedProperty();
    if ($sharedProperty === null) {
      throw new IllegalArgumentException("This line does not have a shared property ($line).");
    }
    $missing = [];
    if (get_class($sharedProperty) === 'Shape') {
      foreach (Color::colors() as $color) {
        $tile = Tile::get($color, $sharedProperty);
        if (!$line->contains($tile)) {
          $missing[] = $tile;
        }
      }
    } elseif (get_class($sharedProperty) === 'Color') {
      foreach (Shape::shapes() as $shape) {
        $tile = Tile::get($sharedProperty, $shape);
        if (!$line->contains($tile)) {
          $missing[] = $tile;
        }
      }
    } else {
      throw new IllegalArgumentException("Unrecognized property $sharedProperty");
    }
    return $missing;
  }

  private function notAccountedFor($searchTile, Board $board) {
    $tiles = Tile::allTiles();
    foreach ($board->tiles() as $tile) {
      if ($tile === $searchTile) {
        $pos = array_search($tile, $tiles);
        unset($tiles[$pos]);
      }
    }
    return in_array($tile, $tiles);
  }
}

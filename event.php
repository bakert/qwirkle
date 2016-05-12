<?php

class Event {
  public function draw(Player $player, array $tiles) {
    Assert::type($tiles, Tile);
    $hand = new Hand($tiles); 
    echo "{$player} draws {$hand}";
    if (count($tiles) < $player->hand()->size()) {
      echo ", hand is {$player->hand()}";
    }
    echo "\n\n";
  }

  public function move(Player $player, Move $move, $score, Board $board, Scores $scores) {
    if (count($move->placements()) > 0) {
      echo "{$player} plays {$move} for {$score}\n";
      echo "\n{$board}";
      echo "\n{$scores}";
    } else {
      echo "{$player} changes his letters.\n";
    }
    echo "\n";
  }

  public function __call($f, array $args) {
    echo "$f\n";
  }
}

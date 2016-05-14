<?php

class Exec {
  public function go() {
    $players = [
      new Player('Qwirky'),
      new Player('Kwirkster'),
      new Player('Mr. Three'),
      new CautiousPlayer('Cautious Player')
    ];
    $game = new Game($players, unserialize(TestGames::FIRST));
    $game->go();
  }
}

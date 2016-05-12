<?php

class Exec {
  public function go() {
    $players = [new Player('Qwirky'), new Player('Kwirkster'), new Player('Mr. Three')];
    $game = new Game($players);
    $game->go();
  }
}


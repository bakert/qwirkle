<?php

class Exec {
  public function go() {
    $players = [
      // new Player('Qwirky'),
      // new Player('Kwirkster'),
      // new Player('Mr. Three'),
      new Player('bakert'),
      new Player('Syl'),
      new Player('Fra')
    ];
    $game = new Game($players, unserialize(TestGames::FIRST));
    $game->go();
    exit;

    $players = [
      // new Player('Qwirky'),
      // new Player('Kwirkster'),
      // new Player('Mr. Three'),
      new CautiousPlayer('bakert'),
      new CautiousPlayer('Syl'),
      new Player('Fra')
    ];
    $totals = [];
    for ($i = 0; $i < 100; $i++) {
      $game = new Game($players/*, unserialize(TestGames::FIRST)*/);
      $game->go();
      foreach ($game->scores()->scores() as $score) {
        if (!isset($totals[(string)$score->player()])) {
          $totals[(string)$score->player()] = 0;
        }
        $totals[(string)$score->player()] += $score->score();
      }
      print_r($totals);
    }
  }
}

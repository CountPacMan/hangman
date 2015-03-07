<?php
  require_once __DIR__."/../src/hangmanDictionary.php";
  
  class Game {
    private $player;
    private $word;
    private $guess_wrong;
    private $guess_total;

    function __construct($player, $guess_wrong = 0, $guess_total = 0) {
      $this->player = $player;
      $wordSelector = new hangmanDictionary();
      $this->word = $wordSelector->getWord();
      $this->guess_wrong = $guess_wrong;
      $this->guess_total = $guess_total;
    }

    // getters
    function getWord() {
      return $this->word;
    }

    function getWrongGuess() {
      return $this->guess_wrong;
    }

    function getTotalGuess() {
      return $this->guess_total;
    }
    
    function getPlayer() {
      return $this->player;
    }
    
    function getWinner() {
      return $this->guess_wrong < 6;
    }

    // increasers  
    function wrongGuess() {
      $this->guess_wrong++;
    }

    function totalGuess() {
      $this->guess_total++;
    }

    // save the particular game result in the session array
    function save() {
      array_push($_SESSION['games'], $this);
    }

    // get existing contancts
    static function getAll() {
      return $_SESSION['games'];
    }

    // delete existing contacts
    static function deleteAll() {
      $_SESSION['games'] = [];
    }
  }
?>

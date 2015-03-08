<?php
  require_once __DIR__."/../src/hangmanDictionary.php";
  
  class Game {
    private $player;
    private $word;
    private $word_left;
    private $guess_wrong;
    private $guess_total;
    private $letters;
    private $output_word;

    function __construct($player, $guess_wrong = 0, $guess_total = 0) {
      $this->player = $player;
      $wordSelector = new hangmanDictionary();
      $this->word = $wordSelector->getWord();
      $this->word_left = $this->word;
      $this->output_word = str_split($this->word);
      for ($i = 0; $i < count($this->output_word); $i++) {
        $this->output_word[$i] = "_";
      }
      $this->guess_wrong = $guess_wrong;
      $this->guess_total = $guess_total;
      $this->letters = "";
    }

    // getters
    function getWord() {
      return $this->word;
    }

    function getWordLeft() {
      return $this->word_left;
    }
    
    function getOutputWord() {
      return $this->output_word;
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
    
    function getLoser() {
      return ($this->guess_wrong > 6);
    }
    
    function getLetters() {
      return $this->letters;
    }    
    
    // setters
    function setWordLeft($word) {
      $this->word_left = $word;
    }
    
    function setOutputWord($word) {
      $this->output_word = $word;
    }
    
    function setLetters($letter) {
      $this->letters .= $letter;
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
    
    // save this particular game
    function saveThisGame() {
      array_push($_SESSION['thisGame'], $this);
    }
    
    // get this particual game
    static function getThisGame() {
      return $_SESSION['thisGame'];
    }

    // get saved games
    static function getAll() {
      return $_SESSION['games'];
    }
    
    // delete saved games
    static function deleteAll() {
      $_SESSION['games'] = [];
    }
  }
?>

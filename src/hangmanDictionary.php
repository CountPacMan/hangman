<?php

class hangmanDictionary {
  private $word;
  private $dictionary = ['biscuit', 'dough', 'hands', 'man'];
  
  function __construct() {
    $randNum = rand(0, 3);
    $this->word = $this->dictionary[$randNum];
  }
  
  function getWord() {
    return $this->word;
  }
}

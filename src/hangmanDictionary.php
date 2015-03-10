<?php

class hangmanDictionary {
  private $word;
  private $dictionary;

  function __construct() {
    // I pulled the mac osx dictionary from /usr/share/dict/words
    // and cat to a file words.tmp
    // This list contained proper nouns, so I striped them with
    // grep -e "^[a-z]*$" words.tmp > words.txt
    $this->dictionary = file(__DIR__."/../src/words.txt");
    $dictionary_length = count($this->dictionary);
    $randNum = rand(0, $dictionary_length);
    $this->word = $this->dictionary[$randNum];
  }

  function getWord() {
    return $this->word;
  }
}

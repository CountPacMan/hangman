<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/game.php";

  session_start();
  if (empty($_SESSION['games'])) {
    $_SESSION['games'] = [];
  }
  if (empty($_SESSION['thisGame'])) {
    $_SESSION['thisGame'] = [];
  }

  $app = new Silex\Application();
  $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));

  // display stats for games
  // start new game
  $app->get("/", function() use ($app) {
    echo "Dictionary size: " . count(file(__DIR__."/../src/words.txt")) . " words";  
    $_SESSION['thisGame'] = [];
    return $app['twig']->render('home.twig', array('games' => Game::getAll()));
  });

  $app->get("/delete_games", function() use ($app) {
    Game::deleteAll();
    return $app['twig']->render('home.twig', array('games' => Game::getAll()));
  });

  $app->post("/hangman", function() use ($app) {
    $newGame = new Game($_POST['name']);
    $newGame->saveThisGame();
    return $app['twig']->render('hangman.twig', array('game' => $newGame));
  });

  $app->post("/hangmanInGame", function() use ($app) {
    $wrong = false;
    $letterPlayed = false;
    $guess = $_POST['guess'];
    $thisGame = Game::getThisGame()[0];
    $wordLeft = $thisGame->getWordLeft();

    // test if letter has already been played
    if (strstr($thisGame->getLetters(), $guess)) {
      $letterPlayed = true;
    }
    // player plays a good guess
    elseif (strstr($wordLeft, $guess) > -1) {
      // strip letter from word_left
      $wordLeft = str_replace($guess, "", $wordLeft);
      $thisGame->setWordLeft($wordLeft);
      // replace underscore with letter in output_word
      for ($i = 0; $i < count($thisGame->getOutputWord()); $i++) {
        if ($thisGame->getWord()[$i] == $guess) {
          $thisGame->setOutputWord($guess, $i);
        }
      }
      $thisGame->totalGuess();
      $thisGame->setLetters($guess);
      // player has won
      if (strlen($wordLeft) == 0) {
        $thisGame->saveThisGame();
        $thisGame->save();
        return $app['twig']->render('winner.twig', array('game' => $thisGame));
      }
    }
    // player plays a bad guess
    else {
      $thisGame->totalGuess();
      $thisGame->wrongGuess();
      $thisGame->setLetters($guess);
      $wrong = true;
      // player has lost
      if ($thisGame->getLoser()) {
        $thisGame->saveThisGame();
        $thisGame->save();
        return $app['twig']->render('loser.twig', array('game' => $thisGame));
      }
    }
    $thisGame->saveThisGame();
    return $app['twig']->render('hangmanGame.twig', array('game' => $thisGame, 'wrong' => $wrong, 'letterPlayed' => $letterPlayed, 'guess' => $guess));
  });

  return $app;
?>

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
    $test1 = new Game("Awesome Mans", 4, 10);
    $test1->save();
    $test2 = new Game("Biscuit Head", 9, 15);
    $test2->save();
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
    print_r($newGame->getOutputWord());
    return $app['twig']->render('hangman.twig', array('game' => $newGame));
  });
  
  $app->post("/hangmanInGame", function() use ($app) {
    $wrong = false;
    $letterPlayed = false;
    $guess = $_POST['guess'];
    $thisGame = Game::getThisGame()[0];
    $wordLeft = $thisGame->getWordLeft();
    echo "<p>guess: " . $guess . "</p>";
    echo "<p>stripos: " . stripos($wordLeft, $guess) . "</p>";
    if (strstr($thisGame->getLetters(), $guess)) {
      $letterPlayed = true;
      echo "<p> letterplayed is true </p>";
    }
    elseif (strstr($wordLeft, $guess) > -1) {
      // strip letter from word_left
      $wordLeft = str_replace($guess, "", $wordLeft);
      $thisGame->setWordLeft($wordLeft);
      // replace underscore with letter in output_word
//      for ($i = 0; i < $thisGame->getWord(); i++) {
//        if ($)
//      }
      $thisGame->setOutputWord(str_replace($guess, "_", $thisGame->getWord));
      $thisGame->totalGuess();
      $thisGame->setLetters($guess);
      $goodGuess = $guess;
      echo "<p> word left: " . $wordLeft . "</p>";
      echo "<p> total guess: " . $thisGame->getTotalGuess() . "</p>";
      if (strlen($wordLeft) == 0) {
        $thisGame->saveThisGame();
        $thisGame->save();
        return $app['twig']->render('winner.twig', array('game' => $thisGame));
      }
    } else {
      $thisGame->totalGuess();
      $thisGame->wrongGuess();
      $thisGame->setLetters($guess);
      $wrong = true;
      echo "wrong guess!";
      if ($thisGame->getLoser()) {
        $thisGame->saveThisGame();
        $thisGame->save();
        return $app['twig']->render('loser.twig', array('game' => $thisGame));
      }
    }
    $thisGame->saveThisGame();
    return $app['twig']->render('hangmanGame.twig', array('game' => $thisGame, 'wrong' => $wrong, 'letterPlayed' => $letterPlayed, 'goodGuess' => $goodGuess, 'guess' => $guess));
  });

  return $app;
?>

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
    $test1 = new Game("Awesome Mans", "ahaha", "4", "10");
    $test1->save();
    $test2 = new Game("Biscuit Head", "boo", "7", "15");
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
    return $app['twig']->render('hangman.twig', array('game' => $newGame));
  });
  
  $app->post("/hangmanInGame", function() use ($app) {
    $wrong = false;
    $guess = $_POST['guess'];
    $thisGame = Game::getThisGame();
    $wordLeft = $thisGame->getWordLeft();
    
    
    if (stripos($wordLeft, $guess) >= 0) {
      // strip letter from word_left
      str_replace($guess, "", $wordLeft);
      $thisGame->setWordLeft($wordLeft);
      $thisGame->totalGuess();
      if (strlen($wordLeft) == 0) {
        $thisGame->saveThisGame();
        return $app['twig']->render('winner.twig', array('game' => $thisGame));
      }
    } else {
      $thisGame->totalGuess();
      $thisGame->wrongGuess();
      $wrong = true;
      if ($thisGame->getLoser()) {
        $thisGame->saveThisGame();
        return $app['twig']->render('loser.twig', array('game' => $thisGame));
      }
    }
    $thisGame->saveThisGame();
    return $app['twig']->render('hangmanGame.twig', array('game' => $thisGame, 'wrong' => $wrong));
  });

  return $app;
?>

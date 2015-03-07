<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/game.php";

  session_start();
  if (empty($_SESSION['games'])) {
    $_SESSION['games'] = [];
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
    return $app['twig']->render('home.twig', array('games' => Game::getAll()));
  });

  $app->post("/create_contact", function() use ($app) {
    $new_contact = new Contact($_POST['name'], $_POST['phone'], $_POST['address']);
    $new_contact->save();
    return $app['twig']->render('created.twig', array('contact' => $_POST));
  });

  $app->get("/delete_contacts", function() use ($app) {
    Contact::deleteAll();
    return $app['twig']->render('deleted.twig');
  });

  return $app;
?>

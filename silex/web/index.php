<?php

/*
 * Projeto Fase 3 - Providers
  Nesta fase você deverá criar um layout para a aplicação, usando o estilo do Bootstrap 3.

  Adicione o css via online (CDN), ou seja, sem baixar nenhum CSS para a aplicação.

  Crie também as páginas para as listagens dos posts e de um post e use o URLGeneratorProvider
  para redirecionamento.
 */


require_once '../vendor/autoload.php';


$app = new \Silex\Application();
$app['debug'] = TRUE;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../view',
));

$app->register(new Silex\Provider\RoutingServiceProvider());

include_once './posts.php';

$app->run();

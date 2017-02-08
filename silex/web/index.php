<?php

/*
 * Projeto Fase 3 - Services

Nesta fase você deverá criar um serviço compartilhado para prover nosso array de posts.
E as rotas criadas usarão este serviço para pegar e mostrar os posts.
 */


require_once '../vendor/autoload.php';


$app = new \Silex\Application();
$app['debug'] = TRUE;


include_once './posts.php';

$app->run();

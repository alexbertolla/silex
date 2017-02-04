<?php

/*
 * Neste fase você deverá criar sua aplicação com Silex, com uma rota:

  /posts/{id}

  Nesta rota o usuário irá acessar o post com respectivo id e aplicação irá
  consultar em um conjunto de 10 post pré-definidos em um array (com id e conteudo),
  retornando o id do post e seu conteúdo.

  Se o id do post não for válido, o sistema deve retornar uma mensagem de erro
 */


require_once '../vendor/autoload.php';


$app = new \Silex\Application();
$app['debug'] = TRUE;


include_once './posts.php';

$app->run();

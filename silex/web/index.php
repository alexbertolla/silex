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


$posts = array(
    array('id' => 1, 'post' => 'post1'),
    array('id' => 2, 'post' => 'post2'),
    array('id' => 3, 'post' => 'post3'),
    array('id' => 4, 'post' => 'post4'),
    array('id' => 5, 'post' => 'post5'),
    array('id' => 6, 'post' => 'post6'),
    array('id' => 7, 'post' => 'post7'),
    array('id' => 8, 'post' => 'post8'),
    array('id' => 9, 'post' => 'post9'),
    array('id' => 10, 'post' => 'post10'),
);


$app->get('/post/{id}', function ($id) use($posts) {

    if (is_numeric($id)) {
        foreach ($posts as $post) {
            if ($id == $post['id']) {
                return $post['id'] . ' - ' . $post['post'];
            }
        }
    } else {
        echo "Valor do id {$id} inválido";
    }
});


$app->run();
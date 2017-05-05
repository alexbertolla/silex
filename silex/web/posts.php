<?php

/**
 * Projeto fase 2 - Controllers

  Nesta fase você deverá separar criar um arquivo separado para realocarmos as rotas de
  posts e mover a lógica de listagem de um post para ele.

  Você deverá criar uma outra rota (/posts) para listar todos posts e seus conteúdos,
  além disto, nesta listagem você deverá criar uma âncora (link), para que possamos
  ser redirecionados para listagem de um post apenas.
 */
$app['posts'] = function () {
    return array(
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
};



$app->get('/posts', function () use($app) {
    return $app['twig']->render('posts.twig', array('posts' => $app['posts']));
})->bind('posts');;

$app->get('/post/{post}', function ($post) use($app) {
    return $app['twig']->render('post.twig', array('post' => $post));
})->bind('post');


$app->get('/post/{id}', function ($id) use($app) {

    if (is_numeric($id)) {
        foreach ($app['posts'] as $post) {
            if ($id == $post['id']) {
                return $post['id'] . ' - ' . $post['post'];
            }
        }
    } else {
        echo "Valor do id {$id} inválido";
    }
});

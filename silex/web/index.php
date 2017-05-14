<?php

/*
  Você deverá criar o sistema de login com cadastro de usuário e o CRUD de post
  só poderá ser acessado por um usuário autenticado.
 */


require_once '../vendor/autoload.php';


require_once './bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SON\Entity\Post;
use SON\Entity\User;

$app->get('/criarUsuario', function (Request $request) use ($app) {
    $username = $request->request->get('username');
    $password = $request->request->get('password');
    $admin = $request->request->get('admin');
    $repo = $app['user_repository'];

    if ($admin) {
        $repo->createAdminUser($username, $password);
    } else {
        $repo->createUser($username, $password);
    }

    return $app->json(array('username' => $username, 'password' => $password, 'admin' => $admin));
});

$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.twig', array(
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');


$app->get('/', function() use ($app) {
    return $app->redirect($app['url_generator']->generate('posts'));
})->bind('inicio');

$app->get('/posts', function() use ($app, $em) {
    $posts = $em->getRepository(Post::class)->findAll();
//    return $app->json($posts);
    return $app['twig']->render('posts.twig', array('posts' => $posts));
})->bind('posts');


$app->get('/post/novo', function () use($app) {
    return $app['twig']->render('formulario_novo.twig', array('id' => '', 'titulo' => '', 'conteudo' => ''));
})->bind('novo');


$app->post('/post/new', function (Symfony\Component\HttpFoundation\Request $request) use( $app, $em) {

    $data = $request->request->all();
    $post = new Post();

    $post->setTitulo($data['titulo']);
    $post->setConteudo($data['conteudo']);

    $em->persist($post);
    $em->flush();
    if ($post->getId()) {
        return $app->redirect($app['url_generator']->generate('sucesso'));
    } else {
        $app->abort(500, 'Erro de processamento');
    }
})->bind('new');


$app->get('/post/editar/{id}', function ($id) use($app, $em) {
    $post = $em->find(Post::class, $id);
    return $app['twig']->render('formulario_editar.twig', array('id' => $post->getId(), 'titulo' => $post->getTitulo(), 'conteudo' => $post->getConteudo()));
})->bind('editar');

$app->post('/post/update/{id}', function (Symfony\Component\HttpFoundation\Request $request) use( $app, $em) {

    $data = $request->request->all();
    $post = new Post();

    $post->setId($data['id']);
    $post->setTitulo($data['titulo']);
    $post->setConteudo($data['conteudo']);

    $em->merge($post);
    $em->flush();
    return $app->redirect($app['url_generator']->generate('novo'));
})->bind('update');

$app->get('/post/excluir/{id}', function ($id) use($app, $em) {
    $post = $em->find(Post::class, $id);
    $em->remove($post);
    $em->flush();

    $post = $em->find(Post::class, $id);
    return (!$post instanceof Post) ? $app->redirect($app['url_generator']->generate('sucesso')) :
            $app->redirect($app['url_generator']->generate('novo'));
})->bind('excluir');




$app->get('/post/sucesso', function() use ($app) {
    return $app['twig']->render('sucesso.twig', array());
})->bind('sucesso');

$app->run();

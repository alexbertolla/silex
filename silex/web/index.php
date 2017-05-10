<?php

/*
 * Nesta fase você deverá criar um CRUD completo para os posts 
  (Inserir, editar, consultar e excluir).

  Assim serão as rotas:

  GET /posts - para listar todos os posts
  GET /post/novo - para abrir o formulário de cadastro de post
  POST /post/new - para cadastrar o post
  GET /post/editar/{id} - para abrir o formulário de edição de post
  POST /post/update/{id} - para atualizar o post
  GET /post/excluir/{id} - para excluir o post

  O post terá os seguintes campos
  id (int e chave primária), titulo (string) e conteudo (string).
 */


require_once '../vendor/autoload.php';


require_once './bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SON\Entity\Post;

$app = new \Silex\Application();
$app['debug'] = TRUE;


$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../view',
));

$app->register(new Silex\Provider\RoutingServiceProvider());



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

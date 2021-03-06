<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager as EventManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Cache\ArrayCache as Cache;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\ClassLoader;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;

$cache = new Doctrine\Common\Cache\ArrayCache;

$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
        $annotationReader, // use reader
        $cache // and a cache driver
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
// load superclass metadata mapping only, into driver chain
// also registers Gedmo annotations.NOTE: you can personalize it
Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
        $driverChain, // our metadata driver chain, to hook into
        $cachedAnnotationReader // our cached annotation reader
);

// now we want to register our application entities,
// for that we need another metadata driver used for Entity namespace
$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
        $cachedAnnotationReader, // our cached annotation reader
        array(__DIR__ . DIRECTORY_SEPARATOR . '../src')
);
// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity namespace
$driverChain->addDriver($annotationDriver, 'SON');

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR . '../vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

// Third, create event manager and hook prefered extension listeners
$evm = new Doctrine\Common\EventManager();
// gedmo extension listeners
// sluggable
$sluggableListener = new Gedmo\Sluggable\SluggableListener;
// you should set the used annotation reader to listener, to avoid creating new one for mapping drivers
$sluggableListener->setAnnotationReader($cachedAnnotationReader);
$evm->addEventSubscriber($sluggableListener);


//getting the EntityManager
$em = EntityManager::create(
                array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'password' => 'root',
            'dbname' => 'silex',
                ), $config, $evm
);


$app = new Silex\Application();
$app['debug'] = TRUE;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../view',
));



$app->register(new \Silex\Provider\RoutingServiceProvider());
$app->register(new SessionServiceProvider());



$app['user_repository'] = function($app) use ($em) {
    $user = new SON\Entity\User();

    $repo = $em->getRepository('SON\Entity\User');
    $repo->setPasswordEncoder($app['security.encoder_factory']->getEncoder($user));

    return $repo;
};


$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'anonymous' => true,
            'pattern' => '^/',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            // lazily load the user_repository
            'users' => function () use ($app) {
                return $app['user_repository'];
            },
            'logout' => array('logout_path' => '/admin/logout'),
        ),
    )
));

$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
);


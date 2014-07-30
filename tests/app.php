<?php

// reading db options set in phpunit.xml
$dbOptions = [
    'driver'    => $GLOBALS['DB_DRIVER'],
    'host'      => $GLOBALS['DB_HOST'],
    'dbname'    => $GLOBALS['DB_DBNAME'],
    'user'      => $GLOBALS['DB_USER'],
    'password'  => $GLOBALS['DB_PASSWD'],
    'charset'   => 'utf8'
];

// initializing silex app in debug mode
$app = new \Silex\Application(['debug' => true]);

// initializing required service providers
$app->register(new \Silex\Provider\TwigServiceProvider(), ['twig.path' => [__DIR__.'/../views']]);
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\DoctrineServiceProvider(), ['db.options' => $dbOptions]);
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new \Silex\Provider\SecurityServiceProvider());
$app->register(new \Silex\Provider\RememberMeServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \User\UserServiceProvider());

// entering test mode
$app['session.test'] = true;
$app['exception_handler']->disable();

// flushing test DB
$app['db']->executeQuery('DELETE FROM user WHERE 1');


return $app;


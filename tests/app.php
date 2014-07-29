<?php


$app = new \Silex\Application([
    'debug' => true
]);
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => [__DIR__.'/../views']
]);
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'mysql_read' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'silex-blog',
            'user'      => 'root',
            'password'  => '123',
            'charset'   => 'utf8'
        ]
    ]
]);
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new \Silex\Provider\SecurityServiceProvider());
$app->register(new \Silex\Provider\RememberMeServiceProvider());
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \User\UserServiceProvider());
$app['session.test']     = true;
$app['exception_handler']->disable();

$app['db']->executeQuery('DELETE FROM user WHERE 1');

return $app;


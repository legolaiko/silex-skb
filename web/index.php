<?php

require_once __DIR__.'/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = [
  'debug' => true
];

$app = new \Silex\Application($config);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => array(
        __DIR__.'/../views'
    )
));
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider());
$app->register(new \Silex\Provider\LocaleServiceProvider());
$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'mysql_read' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'silex-skb',
            'user'      => 'root',
            'password'  => '123',
            'charset'   => 'utf8'
        ]
    ]
]);
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new \Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'admin' => array(
            'pattern' => '^/admin/',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'users' => array(
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        )
    ]
]);

$app->register(new \User\UserServiceProvider());


$app->mount('user', new \User\UserControllerProvider());



$app['translator.domains'] = array(
    'messages' => array(
        'ru' => array(
            'Register new user' => 'Регистрация пользователя',
            'Sign up' => 'Зарегистрироваться',
            'Email' => 'Почта',
            'Password' => 'Пароль',
            'Repeat password' => 'Повторите пароль',
            'Remember me' => 'Запомнить меня',
            'Sign in' => 'Войти'
        )
    ),
    'validators' => array(
        'ru' => array(
            'The password fields must match.' => 'Значения полей должны совпадать.',
            'User name already registered.' => 'Это имя уже зарегестрировано'
        )
    ),
);
$app['translator']->setLocale('ru');

$app->before(function() use ($app) {
    $app['twig']
        ->addGlobal(
            'formLogin',
            $app['user.manager']
                ->createLoginForm()
                ->createView()
        );
});

$app->match('/', function () use ($app) {
    return $app['twig']->render('layout.twig');
})->bind('/');

$app->run();
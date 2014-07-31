<?php
require_once __DIR__.'/../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');
$config = [
  'debug' => true
];

$app = new \Silex\Application($config);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => [__DIR__.'/../views'],
    // 'twig.options' => ['cache' => __DIR__.'/../cache']
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



$app['translator.domains'] = array(
    'messages' => array(
        'ru' => array(
            'Sign up' => 'Зарегистрироваться',
            'Sign in' => 'Войти',
            'Sign out' => 'Выйти',
            'Save' => 'Сохранить',
            'Email' => 'Почта',
            'Nickname' => 'Имя на сайте',
            'Password' => 'Пароль',
            'Current password' => 'Текущий пароль',
            'Change password' => 'Изменить пароль',
            'Repeat password' => 'Повторите пароль',
            'Remember me' => 'Запомнить меня',
            'Profile' => 'Профиль',
            'Edit profile' => 'Редактировать профиль',
            'Bad credentials' => 'Неверное имя пользователя или пароль'
        )
    ),
    'validators' => array(
        'ru' => array(
            'The password fields must match.' => 'Значения полей должны совпадать.',
            'User name already registered.'   => 'Это имя уже зарегестрировано'
        )
    ),
);
$app['translator']->setLocale('ru');

$app['session.storage.handler'] = function() use ($app) {
    return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
        $app['db']->getWrappedConnection(),
        ['db_table' => 'session']
    );
};



$app->match('/', function () use ($app) {
    return $app['twig']->render('layout.twig');
})->bind('/');

$app->run();
<?php


namespace User;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use User\UserController\UserController;
use User\UserFactory\UserFactory;
use User\UserFormType\Extension\UserFormExtension;
use User\UserFormType\UserAuthType;
use User\UserFormType\UserRegisterType;
use User\UserManager\Dbal\UserDbalManager;
use User\UserUniqueConstraint\UserUniqueConstraintValidator;

class UserServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app An Container instance
     */
    public function register(Container $app)
    {
        /** @var Application $app */

        $app['user.salt']            = '1234';
        $app['user.remember_me_key'] = '1234';
        $app['user.class']           = 'User\\UserManager\\Dbal\\UserDbal';


        $app['user.manager'] = function() use ($app) {
            return new UserDbalManager(
                $app['user.class'], $app['user.salt'], $app['db'], $app['security.encoder_factory']
            );
        };

        $app['user.provider'] = function() use ($app) {
            return new UserProvider($app['user.manager'], $app['user.class']);
        };

        $app['user.form.auth'] = function() {
            return new UserAuthType();
        };

        $app['user.form.register'] = function() {
            return new UserRegisterType();
        };

        $app->extend('form.extensions', function($extensions, $app) {
            $extensions[] = new UserFormExtension([
                $app['user.form.auth'],
                $app['user.form.register']
            ]);
            return $extensions;
        });

        if (!isset($app['validator.validator_service_ids'])) {
            $app['validator.validator_service_ids'] = [];
        }

        $app['validator.validator_service_ids'] = $app['validator.validator_service_ids']
            + ['user.validator.unique' => 'user.validator.unique'];

        $app['user.validator.unique'] = function() use ($app) {
            return new UserUniqueConstraintValidator($app['user.manager']);
        };

        $app['security.firewalls'] = function() use ($app) {
            return [
                'user' => array(
                    'anonymous'   => true,
                    'form'        => [
                        'login_path'         => '/user/login',
                        'check_path'         => '/user/login_check',
                        'username_parameter' => 'form_auth[username]',
                        'password_parameter' => 'form_auth[password]',
                        'csrf_parameter'     => 'form_auth[_token]',
                        'with_csrf'          => true,
                        'intention'          => 'form_auth'
                    ],
                    'remember_me' => [
                        'key'                   => $app['user.remember_me_key'],
                        'remember_me_parameter' => 'form_auth[rememberMe]'
                    ],
                    'logout'      => ['logout_path' => '/user/logout'],
                    'users'       => $app['user.provider']
                )
            ];
        };

        $app['user.controller'] = function() use($app) {
            return new UserController(
                $app['form.factory'],
                $app['session'],
                $app['security.last_error'],
                $app['translator'],
                $app['user.manager'],
                $app['twig']
            );
        };

        $app->mount('user', $this);
    }


    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];
        $controllers->match('register', 'user.controller:register');
        $controllers->match('login',    'user.controller:login');
        return $controllers;
    }


} 
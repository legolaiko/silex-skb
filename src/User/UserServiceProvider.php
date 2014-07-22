<?php


namespace User;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use User\UserFactory\UserFactory;
use User\UserMapper\UserMapperDbal;

class UserServiceProvider implements ServiceProviderInterface
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
        $app['user.salt'] = '1234';

        $app['user.class'] = 'User\User';

        $app['user.factory'] = function() use ($app) {
            return new UserFactory($app['user.salt'], $app['user.class']);
        };

        $app['user.mapper'] = function() use ($app) {
            return new UserMapperDbal($app['user.factory'], $app['db']);
        };

        $app['user.manager'] = function() use ($app) {
            // TODO implement 'assertRegistered' method for dependent services
            return new UserManager(
                $app['form.factory'],
                $app['security.encoder_factory'],
                $app['security.authentication_manager'],
                $app['user.mapper']
            );
        };

        $app['user.provider'] = function() use ($app) {
            return new UserProvider($app['user.mapper']);
        };

        $app['security.firewalls'] = function() use ($app) {
            return [
                'user' => array(
                    'anonymous' => true,
                    'form'      => [
                        'login_path'         => '/user/login',
                        'check_path'         => '/user/login_check',
                        'username_parameter' => 'form[username]',
                        'password_parameter' => 'form[password]',
                        'csrf_parameter'     => 'form[_token]',
                        'with_csrf'          => true,
                        'intention'          => 'form'
                    ],
                    'users'     => $app['user.provider']
                )
            ];
        };
    }

} 
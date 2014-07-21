<?php


namespace User;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use User\UserMapper\UserMapper;

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
        $app['user.mapper'] = function() use ($app) {
            return new UserMapper($app['db']);
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
                    'http'      => true,
                    'users'     => $app['user.provider']
                )
            ];
        };
    }

} 
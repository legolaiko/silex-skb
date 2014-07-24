<?php


namespace User;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use User\UserFactory\UserFactory;
use User\UserFormType\UserLoginType;
use User\UserMapper\UserMapperDbal;
use User\UserUniqueConstraint\UserUniqueConstraintValidator;

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
        $app['user.remember_me_key'] = '1234';

        $app['user.class'] = 'User\User';

        $app['user.factory'] = function() use ($app) {
            return new UserFactory($app['user.salt'], $app['user.class']);
        };

        $app['user.mapper'] = function() use ($app) {
            return new UserMapperDbal($app['user.factory'], $app['db']);
        };

        $app['user.manager'] = function() use ($app) {
            return new UserManager(
                $app['form.factory'],
                $app['security.encoder_factory'],
                $app['security'],
                $app['user.mapper']
            );
        };

        $app['user.provider'] = function() use ($app) {
            return new UserProvider($app['user.mapper']);
        };

        if (!isset($app['validator.validator_service_ids'])) {
            $app['validator.validator_service_ids'] = [];
        }

        $app['validator.validator_service_ids'] = $app['validator.validator_service_ids']
            + ['user.validator.unique' => 'user.validator.unique'];

        $app['user.validator.unique'] = function() use ($app) {
            return new UserUniqueConstraintValidator($app['user.mapper']);
        };

        $app['user.form.login'] = function() {
            return new UserLoginType();
        };

        $app['security.firewalls'] = function() use ($app) {
            return [
                'user' => array(
                    'anonymous'   => true,
                    'form'        => [
                        'login_path'         => '/user/login',
                        'check_path'         => '/user/login_check',
                        'username_parameter' => 'auth[username]',
                        'password_parameter' => 'auth[password]',
                        'csrf_parameter'     => 'auth[_token]',
                        'with_csrf'          => true,
                        'intention'          => 'auth'
                    ],
                    'remember_me' => [
                        'key'                   => $app['user.remember_me_key'],
                        'remember_me_parameter' => 'form[rememberMe]'
                    ],
                    'logout'      => ['logout_path' => '/user/logout'],
                    'users'       => $app['user.provider']
                )
            ];
        };
    }

} 
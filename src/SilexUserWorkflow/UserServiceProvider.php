<?php


namespace SilexUserWorkflow;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

use SilexUserWorkflow\Controller\LoginController;
use SilexUserWorkflow\Controller\ProfileController;
use SilexUserWorkflow\Controller\ProfileEditController;
use SilexUserWorkflow\Controller\ProfilePasswordController;
use SilexUserWorkflow\Controller\RegisterController;

use SilexUserWorkflow\Form\Extension\UserFormExtension;
use SilexUserWorkflow\Form\Type\UserAuthType;
use SilexUserWorkflow\Form\Type\UserEditType;
use SilexUserWorkflow\Form\Type\UserPasswordType;
use SilexUserWorkflow\Form\Type\UserRegisterType;

use SilexUserWorkflow\UserManager\Dbal\UserDbalManager;
use SilexUserWorkflow\UserUniqueConstraint\UserUniqueConstraintValidator;
use SilexUserWorkflow\ViewRenderer\TwigRenderer;

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

        $app['user.salt']                = '1234';
        $app['user.remember_me_key']     = '1234';
        $app['user.class']               = 'SilexUserWorkflow\\UserManager\\Dbal\\UserDbal';
        $app['user.default_target_path'] = 'user_profile';


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

        $app['user.form.edit'] = function() {
            return new UserEditType();
        };
        $app['user.form.password'] = function() {
            return new UserPasswordType();
        };

        $app->extend('form.extensions', function($extensions, $app) {
            // to make type accessible via its names
            $extensions[] = new UserFormExtension([
                $app['user.form.auth'],
                $app['user.form.register'],
                $app['user.form.edit'],
                $app['user.form.password']
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
                        'login_path'          => '/user/login',
                        'check_path'          => '/user/login_check',
                        'username_parameter'  => 'user_form_auth[username]',
                        'password_parameter'  => 'user_form_auth[password]',
                        'csrf_parameter'      => 'user_form_auth[_token]',
                        'with_csrf'           => true,
                        'intention'           => 'user_form_auth',
                        'default_target_path' => $app['user.default_target_path']
                    ],
                    'remember_me' => [
                        'key'                   => $app['user.remember_me_key'],
                        'remember_me_parameter' => 'user_form_auth[rememberMe]'
                    ],
                    'logout'      => ['logout_path' => '/user/logout'],
                    'users'       => $app['user.provider']
                )
            ];
        };

        $app['security.access_rules'] = [
            ['^/user/profile', 'ROLE_USER']
        ];

        $app['user.renderer'] = function() use($app) {
            return new TwigRenderer($app['twig']);
        };

        $app['user.controller.login'] = function() use($app) {
            return new LoginController(
                $app['form.factory'],
                $app['session'],
                $app['security.last_error'],
                $app['translator'],
                $app['user.renderer']
            );
        };

        $app['user.controller.register'] = function() use($app) {
            return new RegisterController(
                $app['form.factory'],
                $app['user.manager'],
                $app['user.renderer']
            );
        };

        $app['user.controller.profile'] = function() use($app) {
            return new ProfileController(
                $app['security'],
                $app['user.renderer']
            );
        };

        $app['user.controller.profile.edit'] = function() use($app) {
            return new ProfileEditController(
                $app['form.factory'],
                $app['user.manager'],
                $app['security'],
                $app['user.renderer'],
                $app['url_generator']->generate('user_profile')
            );
        };

        $app['user.controller.profile.password'] = function() use($app) {
            return new ProfilePasswordController(
                $app['form.factory'],
                $app['user.manager'],
                $app['security'],
                $app['user.renderer'],
                $app['url_generator']->generate('user_profile')
            );
        };



        $app->mount('user', $this);
    }


    public function connect(Application $app)
    {
        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];
        $controllers->match('register',         'user.controller.register:handle');
        $controllers->match('login',            'user.controller.login:handle');
        $controllers->match('profile',          'user.controller.profile:handle');
        $controllers->match('profile/edit',     'user.controller.profile.edit:handle');
        $controllers->match('profile/password', 'user.controller.profile.password:handle');
        return $controllers;
    }


} 
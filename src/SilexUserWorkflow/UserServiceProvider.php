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

use SilexUserWorkflow\Form\Extension\ContainerAwareFormExtension;
use SilexUserWorkflow\Form\Listener\PasswordEncoderListener;
use SilexUserWorkflow\Form\Type\Field\NicknameFieldType;
use SilexUserWorkflow\Form\Type\Field\PasswordRepeatedFieldType;
use SilexUserWorkflow\Form\Type\Field\UsernameFieldType;
use SilexUserWorkflow\Form\Type\UserAuthType;
use SilexUserWorkflow\Form\Type\UserEditType;
use SilexUserWorkflow\Form\Type\UserPasswordType;
use SilexUserWorkflow\Form\Type\UserRegisterType;

use SilexUserWorkflow\Mapper\User\Adapter\Dbal\UserAdapter;
use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;
use SilexUserWorkflow\Mapper\User\UserMapper;
use SilexUserWorkflow\UserManager\Dbal\UserDbalManager;
use SilexUserWorkflow\Validation\UserUniqueConstraint\UserUniqueConstraintValidator;
use SilexUserWorkflow\ViewRenderer\TwigRenderer;
use Symfony\Component\PropertyAccess\PropertyAccessor;

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
        $app['user.class']               = 'SilexUserWorkflow\\Mapper\\User\\Entity\\User';
        $app['user.default_target_path'] = 'user_profile';

        $app['user.mapper.fields'] = [
            MappedUserInterface::FIELD_ID         => 'id',
            MappedUserInterface::FIELD_USERNAME   => 'username',
            MappedUserInterface::FIELD_PASSWORD   => 'password',
            MappedUserInterface::FIELD_NICKNAME   => 'nickname',
            MappedUserInterface::FIELD_IS_ENABLED => 'enabled'
        ];

        $app['user.mapper.defaults'] = function() use ($app) {
            return [
                MappedUserInterface::FIELD_SALT  => $app['user.salt'],
                MappedUserInterface::FIELD_ROLES => ['ROLE_USER']
            ];
        };

        $app['user.mapper.adapter.options'] = [];

        $app['user.mapper.adapter'] = function() use($app) {
            return new UserAdapter($app['db'], $app['user.mapper.adapter.options']);
        };

        $app['user.mapper.propertyAccessor'] = function() use($app) {
            return new PropertyAccessor(false, true);
        };

        $app['user.mapper'] = function() use($app) {
            return new UserMapper($app['user.mapper.adapter'], $app['user.mapper.propertyAccessor'],
                $app['user.mapper.fields'], $app['user.mapper.defaults'], $app['user.class']);
        };

        $app['user.provider'] = function() use ($app) {
            return new UserProvider($app['user.mapper'], $app['user.class']);
        };

        $app['user.form.auth'] = function() {
            return new UserAuthType();
        };
        $app['user.form.register'] = function() use ($app) {
            return new UserRegisterType($app['form.listener.passwordEncoder']);
        };
        $app['user.form.edit'] = function() {
            return new UserEditType();
        };
        $app['user.form.password'] = function() use ($app) {
            return new UserPasswordType($app['form.listener.passwordEncoder']);
        };
        $app['user.form.field.passwordRepeated'] = function() {
            return new PasswordRepeatedFieldType();
        };
        $app['user.form.field.username'] = function() {
            return new UsernameFieldType();
        };
        $app['user.form.field.nickname'] = function() {
            return new NicknameFieldType();
        };

        $app['form.listener.passwordEncoder'] = function() use ($app) {
            return new PasswordEncoderListener($app['security.encoder_factory']);
        };

        $app->extend('form.extensions', function($extensions, $app) {
            // to make types accessible via its names
            $extensions[] = new ContainerAwareFormExtension([
                'user.form.auth',
                'user.form.register',
                'user.form.edit',
                'user.form.password',
                'user.form.field.passwordRepeated',
                'user.form.field.username',
                'user.form.field.nickname'
            ], $app);
            return $extensions;
        });

        if (!isset($app['validator.validator_service_ids'])) {
            $app['validator.validator_service_ids'] = [];
        }

        $app['validator.validator_service_ids'] = $app['validator.validator_service_ids']
            + ['user.validator.unique' => 'user.validator.unique'];

        $app['user.validator.unique'] = function() use ($app) {
            return new UserUniqueConstraintValidator($app['user.mapper']);
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

        /*$app['security.access_rules'] = [
            ['^/user/profile', 'ROLE_USER']
        ];*/

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
                $app['user.mapper'],
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
                $app['user.mapper'],
                $app['security'],
                $app['user.renderer'],
                $app['url_generator']->generate('user_profile')
            );
        };

        $app['user.controller.profile.password'] = function() use($app) {
            return new ProfilePasswordController(
                $app['form.factory'],
                $app['user.mapper'],
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
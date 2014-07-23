<?php

namespace User;


use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class UserControllerProvider implements ControllerProviderInterface
{
    private $app;

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        /* @var $controllers ControllerCollection */
        $controllers = $app['controllers_factory'];

        $controllers->match('register', [$this, 'register'])
            ->bind('/user/register');

        $controllers->match('login', [$this, 'login'])
            ->bind('/user/login');

        return $controllers;
    }

    public function login(Request $request)
    {
        /* @var $userManager \User\UserManager */
        $userManager  = $this->app['user.manager'];

        $lastUsername = $this->app['session']->get('_security.last_username');
        $formLogin    = $userManager->createLoginForm($lastUsername);

        $lastError    = $this->app['security.last_error']($request);

        if (null !== $lastError) {
            $lastError = $this->app['translator']->trans($lastError);
            $formLogin->addError(new FormError($lastError));
        }


        return $this->app['twig']->render(
            'user/login.twig', ['formLogin' => $formLogin->createView()]
        );
    }

    public function register(Request $request)
    {
        /* @var $userManager \User\UserManager */
        $userManager  = $this->app['user.manager'];
        $formRegister = $userManager->createRegisterForm();

        $formRegister->handleRequest($request);

        if ($formRegister->isValid()) {
            $user = $formRegister->getData();
            $userManager->registerUser($user);
        }

        return $this->app['twig']->render(
            'user/register.twig', ['formRegister' => $formRegister->createView()]
        );
    }
} 
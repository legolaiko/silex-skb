<?php

namespace User;


use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
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
        $controllers->match('register', [$this, 'register']);
        return $controllers;
    }

    public function register(Request $request)
    {
        /* @var $userManager \User\UserManager */
        $userManager  = $this->app['user.manager'];
        $registerForm = $userManager->createRegisterForm();

        $registerForm->handleRequest($request);

        if ($registerForm->isValid()) {
            echo 1; die;
        }

        return $this->app['twig']->render(
            'user/register.twig', ['formRegister' => $registerForm->createView()]
        );
    }
} 
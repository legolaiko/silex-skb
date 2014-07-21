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

        $this->app->before([$this, 'userWidget']);

        return $controllers;
    }

    public function register(Request $request)
    {
        /* @var $userManager \User\UserManager */
        $userManager  = $this->app['user.manager'];
        $formRegister = $userManager->createRegisterForm();

        $formRegister->handleRequest($request);

        if ($formRegister->isValid()) {
            $userManager->registerUser($formRegister->getData());
        }

        return $this->app['twig']->render(
            'user/register.twig', ['formRegister' => $formRegister->createView()]
        );
    }

    public function userWidget(Request $request)
    {
        /* @var $userManager \User\UserManager */
        $userManager = $this->app['user.manager'];
        $formLogin   = $userManager->createLoginForm();

        if (array_key_exists('signIn', $request->get('form', []))) {
            // login form submitted
            $formLogin->handleRequest($request);
        }

        $this->app['twig']->addGlobal('formLogin', $formLogin->createView());
    }
} 
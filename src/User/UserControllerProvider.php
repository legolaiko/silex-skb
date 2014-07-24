<?php

namespace User;


use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
        // retrieving auth errors
        $lastUsername = $this->app['session']->get('_security.last_username');
        $lastError    = $this->app['security.last_error']($request);

        $formLogin = $this->app['form.factory']->create(
            $this->app['user.form.login'], null, [
                'username' => $lastUsername,
                'action'   => '/user/login_check'
            ]
        );

        if (null !== $lastError) {
            $lastError = $this->app['translator']->trans($lastError);
            $formLogin->addError(new FormError($lastError));
        }

        return $this->render('user/login', ['formLogin' => $formLogin->createView()]);
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
            $userManager->authenticateForced($user);
            $response = new RedirectResponse('/');
        } else {
            $response = $this->app['twig']->render(
                'user/register.twig', ['formRegister' => $formRegister->createView()]
            );
        }

        return $response;
    }

    /**
     * Renders view. Override this method to use custom template engine
     *
     * @param string $view    View path (without extension)
     * @param array  $context View context
     * @return string
     */
    protected function render($view, $context = [])
    {
        return $this->app['twig']->render($view . '.twig', $context);
    }
} 
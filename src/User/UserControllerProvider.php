<?php

namespace User;


use Silex\ControllerCollection;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\Provider\Translation\Translator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use User\UserFactory\UserFactoryInterface;

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

        $controllers->match('register', [$this, 'actionRegister']);
        $controllers->match('login',    [$this, 'actionLogin']);

        return $controllers;
    }

    public function actionLogin(Request $request)
    {
        // retrieving auth error
        /** @var Session $session */
        $session      = $this->app['session'];
        $lastUsername = $session->get('_security.last_username');
        $lastError    = $this->app['security.last_error']($request);

        /** @var FormInterface $formLogin */
        $formLogin = $this->createForm(
            $this->app['user.form.login'], null, [
                'username' => $lastUsername,
                'action'   => '/user/login_check'
            ]
        );

        if (null !== $lastError) {
            /** @var Translator $trans */
            $trans = $this->app['translator'];

            // TODO Move to FormType. Don't know how addError in a FormType context
            $formLogin->addError(new FormError($trans->trans($lastError)));
        }

        return $this->render('user/login', ['formLogin' => $formLogin->createView()]);
    }

    public function actionRegister(Request $request)
    {

        /** @var UserUtils $userUtils */
        $userUtils = $this->app['user.utils'];

        $formRegister = $this->createForm(
            $this->app['user.form.register'],
            $userUtils->getUserMapper()->getUserFactory()->createUser()
        );

        $formRegister->handleRequest($request);

        if ($formRegister->isValid()) {
            $user = $formRegister->getData();
            $userUtils->encodePassword($user);
            $userUtils->getUserMapper()->insertUser($user);
            $userUtils->authenticateForced($user);
            $response = new RedirectResponse('/');
        } else {
            $response = $this->render(
                'user/register', ['formRegister' => $formRegister->createView()]
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
        /** @var \Twig_Environment $twig */
        $twig = $this->app['twig'];
        return $twig->render($view . '.twig', $context);
    }

    protected function createForm($form = 'form', $data = null, $options = [])
    {
        /** @var FormFactory $formFactory */
        $formFactory = $this->app['form.factory'];
        return $formFactory->create($form, $data, $options);
    }
} 
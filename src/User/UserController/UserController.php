<?php


namespace User\UserController;


use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use User\UserManager\UserManagerInterface;

class UserController {
    
    protected $formFactory;
    protected $session;
    protected $lastErrorCallable;
    protected $translator;
    protected $userManager;
    protected $twig;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        callable $lastErrorCallable,
        TranslatorInterface $translator,
        UserManagerInterface $userManager,
        \Twig_Environment $twig
    )
    {
        $this->formFactory       = $formFactory;
        $this->session           = $session;
        $this->lastErrorCallable = $lastErrorCallable;
        $this->translator        = $translator;
        $this->userManager       = $userManager;
        $this->twig              = $twig;
    }

    public function login(Request $request)
    {
        $lastUsername = $this->session->get('_security.last_username');
        $lastError    = $this->lastErrorCallable;
        $lastError    = $lastError($request);

        $formLogin = $this->formFactory->create(
            'form_auth', null, [
                'username' => $lastUsername,
                'action'   => '/user/login_check'
            ]
        );

        if (null !== $lastError) {
            $lastError = $this->translator->trans($lastError);
            $formLogin->addError(new FormError($lastError));
        }

        return $this->render('user/login', ['formLogin' => $formLogin->createView()]);
    }

    public function register(Request $request)
    {
        $formRegister = $this->formFactory->create(
            'form_register',
            $this->userManager->createUser()
        );

        $formRegister->handleRequest($request);

        if ($formRegister->isValid()) {
            $user = $formRegister->getData();
            $this->userManager->insertUser($user);
            /*$this->userManager->authenticateForced($user);
            $this->securityContext->setToken(new UsernamePasswordToken($user, null, $providerKey));*/
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
        return $this->twig->render($view . '.twig', $context);
    }
} 
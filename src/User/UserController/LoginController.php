<?php


namespace User\UserController;


use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use User\ViewRenderer\RendererInterface;

class LoginController
{
    protected $formFactory;
    protected $session;
    protected $lastErrorCallable;
    protected $translator;
    protected $renderer;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        callable $lastErrorCallable,
        TranslatorInterface $translator,
        RendererInterface $renderer
    )
    {
        $this->formFactory       = $formFactory;
        $this->session           = $session;
        $this->lastErrorCallable = $lastErrorCallable;
        $this->translator        = $translator;
        $this->renderer          = $renderer;
    }

    public function handle(Request $request)
    {
        $lastUsername = $this->session->get('_security.last_username');
        $lastError    = $this->lastErrorCallable;
        $lastError    = $lastError($request);

        $formLogin = $this->formFactory->create(
            'user_form_auth', null, [
                'username' => $lastUsername,
                'action'   => '/user/login_check'
            ]
        );

        if (null !== $lastError) {
            $lastError = $this->translator->trans($lastError);
            $formLogin->addError(new FormError($lastError));
        }

        return $this->renderer->render('user/login', ['formLogin' => $formLogin->createView()]);
    }
} 
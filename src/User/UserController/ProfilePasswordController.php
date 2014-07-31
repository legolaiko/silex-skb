<?php


namespace User\UserController;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use User\UserManager\UserManagerInterface;
use User\ViewRenderer\RendererInterface;

class ProfilePasswordController
{
    protected $formFactory;
    protected $userManager;
    protected $security;
    protected $renderer;
    protected $redirectUrl;

    public function __construct(
        FormFactoryInterface $formFactory, UserManagerInterface $userManager,
        SecurityContextInterface $security, RendererInterface $renderer, $successRedirectUrl
    )
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->security    = $security;
        $this->renderer    = $renderer;
        $this->redirectUrl = $successRedirectUrl;
    }

    public function handle(Request $request)
    {
        $formProfile = $this->formFactory->create('user_form_password');
        $formProfile->handleRequest($request);
        if ($formProfile->isValid()) {
            $user = $this->security->getToken();
            if (null === $user) {
                throw new \LogicException('Token must not be null');
            }
            $user = $user->getUser();
            $user->setPassword($formProfile->getData()['password']);
            $this->userManager->updateUser($user);
            $response = new RedirectResponse($this->redirectUrl);
        } else {
            $response = $this->renderer->render('user/profile-password', [
                'formPassword' => $formProfile->createView()
            ]);
        }
        return $response;
    }
}
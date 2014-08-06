<?php


namespace SilexUserWorkflow\Controller;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use SilexUserWorkflow\UserManager\UserManagerInterface;
use SilexUserWorkflow\ViewRenderer\RendererInterface;

class ProfileEditController
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
        $user = $this->security->getToken();
        if (null === $user) {
            throw new \LogicException('Token must not be null');
        }
        $user = clone $user->getUser();
        $formProfile = $this->formFactory->create('user_form_edit', $user);
        $formProfile->handleRequest($request);
        if ($formProfile->isValid()) {
            $this->userManager->updateUser($user);
            $response = new RedirectResponse($this->redirectUrl);
        } else {
            $response = $this->renderer->render('user/profile-edit', [
                'formProfile' => $formProfile->createView()
            ]);
        }
        return $response;
    }
}
<?php


namespace User\UserController;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use User\UserManager\UserManagerInterface;
use User\ViewRenderer\RendererInterface;

class RegisterController {
    
    protected $formFactory;
    protected $userManager;
    protected $renderer;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserManagerInterface $userManager,
        RendererInterface    $renderer
    )
    {
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->renderer    = $renderer;
    }

    public function handle(Request $request)
    {
        $formRegister = $this->formFactory->create(
            'user_form_register',
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
            $response = $this->renderer->render(
                'user/register', ['formRegister' => $formRegister->createView()]
            );
        }

        return $response;
    }
} 
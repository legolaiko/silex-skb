<?php


namespace SilexUserWorkflow\Controller;

use SilexUserWorkflow\Mapper\User\UserMapperInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use SilexUserWorkflow\ViewRenderer\RendererInterface;

class RegisterController {
    
    protected $formFactory;
    protected $userMapper;
    protected $renderer;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserMapperInterface $userMapper,
        RendererInterface    $renderer
    )
    {
        $this->formFactory = $formFactory;
        $this->userMapper  = $userMapper;
        $this->renderer    = $renderer;
    }

    public function handle(Request $request)
    {
        $formRegister = $this->formFactory->create(
            'user_form_register',
            $this->userMapper->create()
        );

        $formRegister->handleRequest($request);

        if ($formRegister->isValid()) {
            $user = $formRegister->getData();
            $this->userMapper->save($user);
            /*$this->userManager->authenticateForced($user);
            $this->securityContext->setToken(new UsernamePasswordToken($user, null, $providerKey));*/
            //$response = new RedirectResponse('/');
        } else {
            $response = $this->renderer->render(
                'user/register', ['formRegister' => $formRegister->createView()]
            );
        }

        $response = $this->renderer->render(
            'user/register', ['formRegister' => $formRegister->createView()]
        );

        return $response;
    }
} 
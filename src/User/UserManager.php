<?php


namespace User;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use User\UserMapper\UserMapperInterface;
use User\UserUniqueConstraint\UserUniqueConstraint;

class UserManager
{
    protected $formFactory;
    protected $encoderFactory;
    protected $authManager;
    protected $userMapper;

    function __construct(
        FormFactoryInterface $formFactory,
        EncoderFactoryInterface $encoderFactory,
        AuthenticationManagerInterface $authManager,
        UserMapperInterface $userMapper)
    {
        $this->formFactory    = $formFactory;
        $this->encoderFactory = $encoderFactory;
        $this->authManager    = $authManager;
        $this->userMapper     = $userMapper;
    }

    public function createLoginForm()
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('username', 'email', [
                'constraints' => [
                    new Email(),
                    new Length(['min' => 4, 'max' => 128]),
                    new UserUniqueConstraint($this->userMapper),
                    new NotBlank()
                ],
                'label' => 'Email'
            ])
            ->add('password', 'password')
            ->add('rememberMe', 'checkbox', ['required' => false])
            ->add('signIn', 'submit')
            ->getForm();

        return $form;
    }

    public function createRegisterForm()
    {
        $user = $this->userMapper->getUserFactory()->createUser();

        $form = $this->formFactory
            ->createBuilder('form', $user)
            ->add('username', 'email', [
                'constraints' => [
                    new Email(),
                    new Length(['min' => 4, 'max' => 128]),
                    new UserUniqueConstraint($this->userMapper),
                    new NotBlank()
                ],
                'label' => 'Email'
            ])
            ->add('password', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'   => ['label' => 'Password'],
                'second_options'  => ['label' => 'Repeat password'],
                'constraints'     => [
                    new Length(['min' => 4, 'max' => 64]),
                    new NotBlank()
                ]
            ])
            ->add('signUp', 'submit')
            ->getForm();
        return $form;
    }


    public function registerUser(User $user, $isPasswordEncoded = false)
    {
        if (!$isPasswordEncoded) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $pwdEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($pwdEncoded);
        }

        $this->userMapper->insertUser($user);
    }

    public function authenticate($username, $password, $providerKey = 'user')
    {
        try {
            $this->authManager->authenticate(new UsernamePasswordToken($username, $password, $providerKey));
        } catch (\Exception $e) {

        }

    }
} 
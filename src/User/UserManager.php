<?php


namespace User;


use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use User\UserFactory\UserWritableInterface;
use User\UserMapper\UserMapperInterface;
use User\UserUniqueConstraint\UserUniqueConstraint;

class UserManager
{
    protected $formFactory;
    protected $encoderFactory;
    protected $securityContext;
    protected $userMapper;

    function __construct(
        FormFactoryInterface $formFactory,
        EncoderFactoryInterface $encoderFactory,
        SecurityContextInterface $securityContext,
        UserMapperInterface $userMapper)
    {
        $this->formFactory     = $formFactory;
        $this->encoderFactory  = $encoderFactory;
        $this->securityContext = $securityContext;
        $this->userMapper      = $userMapper;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function createLoginForm($username = null)
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('username', 'email', [
                'label' => 'Email',
                'data' => $username
            ])
            ->add('password', 'password')
            ->add('rememberMe', 'checkbox', ['required' => false])
            ->add('signIn', 'submit')
            ->setAction('/user/login_check')
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
                    new UserUniqueConstraint(),
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


    public function registerUser(UserWritableInterface $user)
    {
        $encoder    = $this->encoderFactory->getEncoder($user);
        $pwdEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($pwdEncoded);
        $this->userMapper->insertUser($user);
        return $this;
    }

    public function authenticateForced(UserInterface $user, $providerKey = 'user')
    {
        $this->securityContext->setToken(new UsernamePasswordToken($user, null, $providerKey));
    }
} 
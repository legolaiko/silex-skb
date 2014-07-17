<?php


namespace User;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class UserManager
{
    protected $formFactory;
    protected $encoderFactory;
    protected $salt;

    function __construct(FormFactoryInterface $formFactory, EncoderFactoryInterface $encoderFactory, $salt = 'ladkfn34')
    {
        $this->formFactory    = $formFactory;
        $this->encoderFactory = $encoderFactory;
        $this->salt           = $salt;
    }

    public function createLoginForm()
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('email', 'text')
            ->add('password', 'password')
            ->add('rememberMe', 'checkbox', ['required' => false])
            ->add('signIn', 'submit')
            ->getForm();

        return $form;
    }

    public function createRegisterForm()
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('email', 'email', [
                'constraints' => [new Email(), new Length(['min' => 4, 'max' => 128])]])
            ->add('password', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'   => ['label' => 'Password'],
                'second_options'  => ['label' => 'Repeat password'],
                'constraints' => new Length(['min' => 4, 'max' => 64])
            ])
            ->add('signUp', 'submit')
            ->getForm();
        return $form;
    }

    public function registerUser($email, $plainPassword)
    {
        $user    = new User($email, $plainPassword);
        $encoder = $this->encoderFactory->getEncoder($user);

    }
} 
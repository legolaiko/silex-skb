<?php


namespace User;


use Symfony\Component\Form\FormFactory;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;

class UserManager
{
    protected $formFactory;
    protected $translator;

    function __construct(FormFactory $formFactory, TranslatorInterface $translator)
    {
        $this->formFactory = $formFactory;
        $this->translator  = $translator;
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
            ->add('email', 'text', ['constraints' => new Email()])
            ->add('password', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat password'),
            ])
            ->add('signUp', 'submit')
            ->getForm();
        return $form;
    }
} 
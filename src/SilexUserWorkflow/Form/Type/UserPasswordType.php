<?php

namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserPasswordType extends AbstractType
{
    protected $passwordEncoderListener;

    public function __construct(callable $passwordEncoderListener)
    {
        $this->passwordEncoderListener = $passwordEncoderListener;
    }

    public function getName()
    {
        return 'user_form_password';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', 'password', [
                'constraints' => new UserPassword(),
                'mapped'      => false
            ])
            ->add('password', 'user_form_field_passwordRepeated')
            ->add('Save',   'submit')
            ->addEventListener(FormEvents::POST_SUBMIT, $this->passwordEncoderListener);;
    }


} 
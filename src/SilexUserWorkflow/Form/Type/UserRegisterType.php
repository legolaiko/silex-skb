<?php

namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class UserRegisterType extends AbstractType
{
    protected $passwordEncoderListener;

    public function __construct(callable $passwordEncoderListener)
    {
        $this->passwordEncoderListener = $passwordEncoderListener;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_form_register';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'user_form_field_username')
            ->add('nickname', 'user_form_field_nickname')
            ->add('password', 'user_form_field_passwordRepeated')
            ->add('signUp',   'submit')
            ->addEventListener(FormEvents::POST_SUBMIT, $this->passwordEncoderListener);
    }


}
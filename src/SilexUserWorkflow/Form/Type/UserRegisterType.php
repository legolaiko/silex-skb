<?php

namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;

class UserRegisterType extends UserAbstractType
{
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
            ->add('username', 'email',    $this->getUsernameOptions())
            ->add('nickname', 'text',     $this->getNicknameOptions())
            ->add('password', 'repeated', $this->getPasswordRepeatedOptions())
            ->add('signUp',   'submit');
    }


} 
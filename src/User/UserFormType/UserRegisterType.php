<?php

namespace User\UserFormType;


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
        return 'register';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'email',    $this->getUsernameOptions())
            ->add('password', 'repeated', $this->getPasswordRepeatedOptions())
            ->add('signUp',   'submit');
    }


} 
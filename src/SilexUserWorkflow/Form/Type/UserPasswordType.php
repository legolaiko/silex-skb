<?php

namespace SilexUserWorkflow\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserPasswordType extends UserAbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_form_password';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', 'password', [
                'constraints' => new UserPassword()
            ])
            ->add('password', 'repeated', $this->getPasswordRepeatedOptions())
            ->add('Save',   'submit');
    }


} 
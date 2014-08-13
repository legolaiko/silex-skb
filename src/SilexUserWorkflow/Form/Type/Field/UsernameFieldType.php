<?php


namespace SilexUserWorkflow\Form\Type\Field;


use SilexUserWorkflow\Validation\UserUniqueConstraint\UserUniqueConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsernameFieldType extends AbstractType
{
    public function getName()
    {
        return 'user_form_field_username';
    }

    public function getParent()
    {
        return 'email';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Email',
            'constraints' => [
                new Email(),
                new Length(['min' => 4, 'max' => 128]),
                new UserUniqueConstraint(),
                new NotBlank()
            ]
        ]);
    }
} 
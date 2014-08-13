<?php


namespace SilexUserWorkflow\Form\Type\Field;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordRepeatedFieldType extends AbstractType
{
    public function getName()
    {
        return 'user_form_field_passwordRepeated';
    }

    public function getParent()
    {
        return 'repeated';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'first_options'   => ['label' => 'Password'],
            'second_options'  => ['label' => 'Repeat password'],
            'constraints'     => [
                new Length(['min' => 4, 'max' => 64]),
                new NotBlank()
            ]
        ]);
    }


} 
<?php


namespace SilexUserWorkflow\Form\Type\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class NicknameFieldType extends AbstractType
{
    public function getName()
    {
        return 'user_form_field_nickname';
    }

    public function getParent()
    {
        return 'text';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new Length(['min' => 2, 'max' => 128]),
                new Regex([
                    'pattern' => '/^[a-zA-Za0-9]+$/'
                ]),
                new NotBlank()
            ]
        ]);
    }
} 
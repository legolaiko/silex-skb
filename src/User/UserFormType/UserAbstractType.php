<?php

namespace User\UserFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use User\UserUniqueConstraint\UserUniqueConstraint;

abstract class UserAbstractType extends AbstractType
{
    protected function getUsernameOptions()
    {
        return [
            'constraints' => [
                new Email(),
                new Length(['min' => 4, 'max' => 128]),
                new UserUniqueConstraint(),
                new NotBlank()
            ]
        ];
    }

    protected function getPasswordRepeatedOptions()
    {
        return [
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'first_options'   => ['label' => 'Password'],
            'second_options'  => ['label' => 'Repeat password'],
            'constraints'     => [
                new Length(['min' => 4, 'max' => 64]),
                new NotBlank()
            ]
        ];
    }



} 
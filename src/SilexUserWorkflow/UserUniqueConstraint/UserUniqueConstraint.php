<?php


namespace SilexUserWorkflow\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;

class UserUniqueConstraint extends Constraint
{
    public $message = 'User name already registered.';

    public function validatedBy()
    {
        return 'user.validator.unique';
    }
} 
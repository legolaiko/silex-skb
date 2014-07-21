<?php


namespace User\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserUniqueConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \User\UserUniqueConstraint\UserUniqueConstraint */

        $user = $constraint->getUserMapper()->findByUsername($value);
        if ($user) {
            $this->context->addViolation($constraint->getMessage());
        }
    }

} 
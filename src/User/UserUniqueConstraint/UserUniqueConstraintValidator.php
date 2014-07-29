<?php


namespace User\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use User\UserManager\UserManagerInterface;

class UserUniqueConstraintValidator extends ConstraintValidator
{
    protected $userMapper;

    public function __construct(UserManagerInterface $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \User\UserUniqueConstraint\UserUniqueConstraint */

        $user = $this->userMapper->findByUsername($value);
        if ($user) {
            $this->context->addViolation($constraint->message);
        }
    }

} 
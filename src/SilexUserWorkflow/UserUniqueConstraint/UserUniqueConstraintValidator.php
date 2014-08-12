<?php


namespace SilexUserWorkflow\UserUniqueConstraint;


use SilexUserWorkflow\Mapper\User\UserMapperInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserUniqueConstraintValidator extends ConstraintValidator
{
    protected $userMapper;

    public function __construct(UserMapperInterface $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \SilexUserWorkflow\UserUniqueConstraint\UserUniqueConstraint */

        $user = $this->userMapper->findByUsername($value);
        if ($user) {
            $this->context->addViolation($constraint->message);
        }
    }

} 
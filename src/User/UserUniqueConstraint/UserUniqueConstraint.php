<?php


namespace User\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;
use User\UserManager;

class UserUniqueConstraint extends Constraint
{
    protected $userManager;
    protected $message = 'User name already registered';

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
} 
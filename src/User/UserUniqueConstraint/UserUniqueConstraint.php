<?php


namespace User\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;
use User\UserMapper\UserMapper;

class UserUniqueConstraint extends Constraint
{
    protected $userMapper;
    protected $message = 'User name already registered.';

    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return UserMapper
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }
} 
<?php


namespace User\UserUniqueConstraint;


use Symfony\Component\Validator\Constraint;
use User\UserMapper\UserMapperInterface;

class UserUniqueConstraint extends Constraint
{
    protected $userMapper;
    protected $message = 'User name already registered.';

    public function __construct(UserMapperInterface $userMapper)
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
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }
} 
<?php


namespace User\UserFactory;


use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFactory implements UserFactoryInterface
{
    protected $salt;
    protected $userClass;

    public function __construct($salt, $userClass)
    {
        $this->salt      = $salt;
        $this->userClass = $userClass;
    }

    /**
     * Creates new user
     * @return UserInterface
     */
    public function createUser()
    {
        $user = new $this->userClass;
        if (!($user instanceof UserWritableInterface)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        $user->setSalt($this->salt);
        $user->setRoles(['ROLE_USER']);
    }

    /**
     * Gets class name for factored users
     * @return string
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

}
<?php


namespace SilexUserWorkflow;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use SilexUserWorkflow\UserManager\UserManagerInterface;

class UserProvider implements UserProviderInterface {

    protected $userMapper;
    protected $supportedClass;

    public function __construct(UserManagerInterface $userMapper, $supportedClass)
    {
        $this->userMapper     = $userMapper;
        $this->supportedClass = $supportedClass;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userMapper->findByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === $this->supportedClass;
    }
} 
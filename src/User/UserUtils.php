<?php

namespace User;



use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use User\UserFactory\UserWritableInterface;
use User\UserMapper\UserMapperInterface;

class UserUtils
{
    protected $encoderFactory;
    protected $securityContext;
    protected $userMapper;

    function __construct(
        EncoderFactoryInterface  $encoderFactory,
        SecurityContextInterface $securityContext,
        UserMapperInterface      $userMapper)
    {
        $this->encoderFactory  = $encoderFactory;
        $this->securityContext = $securityContext;
        $this->userMapper      = $userMapper;
    }

    public function encodePassword(UserWritableInterface $user)
    {
        $encoder    = $this->encoderFactory->getEncoder($user);
        $pwdEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($pwdEncoded);

        return $this;
    }

    public function authenticateForced(UserInterface $user, $providerKey = 'user')
    {
        $this->securityContext->setToken(new UsernamePasswordToken($user, null, $providerKey));
    }

    /**
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }


} 
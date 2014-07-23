<?php

namespace User\UserFactory;


use Symfony\Component\Security\Core\User\UserInterface;

interface UserWritableInterface extends UserInterface
{
    public function setUsername($username);

    public function setPassword($password);

    public function setEnabled($isEnabled);

    public function setSalt($salt);

    public function setRoles($roles);
} 
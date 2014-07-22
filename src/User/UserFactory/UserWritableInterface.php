<?php

namespace User\UserFactory;


interface UserWritableInterface
{
    public function setUsername($username);

    public function setPassword($password);

    public function setSalt($salt);

    public function setRoles($roles);
} 
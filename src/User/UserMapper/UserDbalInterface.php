<?php

namespace User\UserMapper;


use Symfony\Component\Security\Core\User\UserInterface;
use User\UserFactory\UserWritableInterface;

interface UserDbalInterface extends UserWritableInterface
{
    public function getId();

    public function setId($id);
} 
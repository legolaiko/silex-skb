<?php

namespace User\UserMapper;

use User\UserFactory\UserWritableInterface;

interface UserDbalInterface extends UserWritableInterface
{
    public function getId();

    public function setId($id);
} 
<?php

namespace User\UserManager\Dbal;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface UserDbalInterface extends AdvancedUserInterface
{
    public function getId();

    public function setId($id);

    public function getNickname();

    public function setNickname($nickname);

    public function setUsername($username);

    public function setPassword($password);

    public function setEnabled($isEnabled);

    public function setSalt($salt);

    public function setRoles($roles);
} 
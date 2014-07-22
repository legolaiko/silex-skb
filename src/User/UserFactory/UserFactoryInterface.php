<?php

namespace User\UserFactory;


use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{
    /**
     * Creates new user
     * @return UserInterface
     */
    public function createUser();

    /**
     * Gets class name for factored users
     * @return string
     */
    public function getUserClass();
} 
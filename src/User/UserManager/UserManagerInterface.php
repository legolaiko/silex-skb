<?php

namespace User\UserManager;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{

    /**
     * Creates new user, factory method
     *
     * @return UserInterface
     */
    public function createUser();

    /**
     * Inserts new user to storage
     *
     * @param UserInterface $user
     * @return void
     */
    public function insertUser(UserInterface $user);

    /**
     * Updates existing user in storage
     *
     * @param UserInterface $user
     * @return void
     */
    public function updateUser(UserInterface $user);

    /**
     * Extracts user from storage by username
     *
     * @param $username
     * @return UserInterface
     */
    public function findByUsername($username);
} 
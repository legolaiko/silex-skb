<?php

namespace User\UserMapper;

use Symfony\Component\Security\Core\User\UserInterface;
use User\UserFactory\UserFactoryInterface;

interface UserMapperInterface
{
    /**
     * Stores new user to storage
     *
     * @param UserInterface $user
     * @return void
     */
    public function insertUser(UserInterface $user);

    /**
     * Extracts user from storage by username
     *
     * @param $username
     * @return UserInterface
     */
    public function findByUserName($username);

    /**
     * Gets UserFactoryInterface associated with mapper
     *
     * @return UserFactoryInterface
     */
    public function getUserFactory();
} 
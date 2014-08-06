<?php


namespace SilexUserWorkflow\Mapper\User;


use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;

interface MapperInterface
{
    /**
     * Creates new user, factory method
     *
     * @return MappedUserInterface
     */
    public function create();

    /**
     * Saves (inserts new or updates existing one) user to storage
     *
     * @param MappedUserInterface $user
     * @return void
     */
    public function save(MappedUserInterface $user);

    /**
     * Finds user in storage by username
     *
     * @param $username
     * @return MappedUserInterface
     */
    public function findByUsername($username);
} 
<?php


namespace SilexUserWorkflow\Mapper\User\Adapter;


interface UserAdapterInterface
{
    public function insertUser(array $data);

    public function findUser(array $criteria);

    public function updateUser(array $criteria, array $data);

    public function deleteUser(array $criteria);

    public function findUserRoles($userId);

    public function replaceUserRoles($userId, array $roles);
} 
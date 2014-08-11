<?php


namespace SilexUserWorkflow\Mapper\User\Adapter;


interface AdapterInterface
{
    public function insertUser(array $data);

    public function findUser(array $criteria);

    public function updateUser(array $criteria, array $data);

    public function deleteUser(array $criteria);

    public function findUserRoles($userId);

    public function replaceUserRoles($userId, array $roles);
} 
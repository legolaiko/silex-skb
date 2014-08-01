<?php


namespace User\AccessTokenStorage\Adapter;


interface StorageAdapterInterface
{
    const FIELD_TOKEN = 'token';
    const FIELD_DATA  = 'data';

    /**
     * Inserts row to persistent storage. Must return false if given token is not unique
     *
     * @param array $row
     * @return bool True on successful insertion
     */
    public function insert(array $row);

    /**
     * Finds row in storage
     *
     * @param string $token
     * @return array|false Token row
     */
    public function findByToken($token);
} 
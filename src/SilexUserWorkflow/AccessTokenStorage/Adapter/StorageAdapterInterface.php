<?php


namespace SilexUserWorkflow\AccessTokenStorage\Adapter;

/**
 * Interface StorageAdapterInterface
 * Provides CRUD for specific storage strategy
 * @package User\AccessTokenStorage
 */
interface StorageAdapterInterface
{

    /**
     * Creates record in storage, if given token is unique (else must return false)
     *
     * @param string $token
     * @param string $data
     * @return bool True on successful insertion
     */
    public function createUnique($token, $data);

    /**
     * Finds token data in storage
     *
     * @param string $token
     * @return string|false Token data
     */
    public function read($token);

    /**
     * Updates existing token in storage
     *
     * @param string $token
     * @param string $data
     * @return void
     */
    public function update($token, $data);

    /**
     * Removes token from storage
     *
     * @param $token
     * @return void
     */
    public function delete($token);


} 
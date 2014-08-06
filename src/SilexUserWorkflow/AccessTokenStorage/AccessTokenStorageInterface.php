<?php

namespace SilexUserWorkflow\AccessTokenStorage;


interface AccessTokenStorageInterface
{
    /**
     * Creates data in storage
     *
     * @param mixed $data Data to store
     * @return string Access token
     */
    public function createData($data);

    /**
     * Reads data from storage
     *
     * @param string $accessToken Unique access token, given by @see createData
     * @return mixed Stored data
     */
    public function readData($accessToken);

    /**
     * Updates data in storage
     *
     * @param string $accessToken Unique access token, given by @see createData
     * @param mixed $data Data to update
     * @return void
     */
    public function updateData($accessToken, $data);

    /**
     * Deletes data
     *
     * @param string $accessToken Unique access token, given by @see createData
     * @return void
     */
    public function deleteData($accessToken);
} 
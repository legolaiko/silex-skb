<?php

namespace User\AccessTokenStorage;


interface AccessTokenStorageInterface
{
    /**
     * Puts data to storage
     *
     * @param mixed $data Data to store
     * @return string Access token
     */
    public function putData($data);

    /**
     * Gets data from storage
     *
     * @param string $accessToken Unique access token, given by @see putData
     * @return mixed Stored data
     */
    public function getData($accessToken);
} 
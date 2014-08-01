<?php

namespace User\AccessTokenStorage;


interface AccessTokenGeneratorInterface
{
    /**
     * Generates unique and secured access token
     *
     * @return string
     */
    public function generateToken();
} 
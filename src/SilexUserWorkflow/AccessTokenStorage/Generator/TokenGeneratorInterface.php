<?php

namespace SilexUserWorkflow\AccessTokenStorage\Generator;


interface TokenGeneratorInterface
{
    /**
     * Generates unique and secured access token
     *
     * @return string
     */
    public function generateToken();
} 
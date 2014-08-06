<?php


namespace SilexUserWorkflow\UserManager\Dbal;


class PasswordEncoded
{

    protected $passwordEncoded;

    public function __construct($passwordEncoded)
    {
        $this->passwordEncoded = $passwordEncoded;
    }

    function __toString()
    {
        return $this->passwordEncoded;
    }
} 
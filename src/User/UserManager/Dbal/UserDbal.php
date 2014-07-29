<?php


namespace User\UserManager\Dbal;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class UserDbal implements UserDbalInterface
{

    protected $id;
    protected $isAccountNonExpired = true;
    protected $isAccountNonLocked  = true;
    protected $isEnabled           = true;
    protected $roles               = [];
    protected $username;
    protected $password;
    protected $salt;

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->isAccountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @param string $username
     * @return UserDbal
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param string $password
     * @return UserDbal
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $salt
     * @return UserDbal
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }
} 
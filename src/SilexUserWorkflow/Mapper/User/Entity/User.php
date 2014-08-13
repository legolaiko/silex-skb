<?php

namespace SilexUserWorkflow\Mapper\User\Entity;

/**
 * Class User
 * MappedUserInterface entity implementation
 * @package SilexUserWorkflow
 */
class User implements MappedUserInterface
{

    protected $isAccountNonExpired     = true;
    protected $isAccountNonLocked      = true;
    protected $isCredentialsNonExpired = true;
    protected $isEnabled               = true;

    protected $isPasswordEncoded       = false;
    protected $isRolesDirt             = true;

    protected $username;
    protected $password;
    protected $roles                   = [];
    protected $salt;

    protected $id;
    protected $nickname;

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired($isAccountNonExpired = null)
    {
        if (null !== $isAccountNonExpired) {
            $this->isAccountNonExpired = $isAccountNonExpired;
        }
        return $this->isAccountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked($isAccountNonLocked = null)
    {
        if (null !== $isAccountNonLocked) {
            $this->isAccountNonLocked = $isAccountNonLocked;
        }
        return $this->isAccountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired($isCredentialsNonExpired = null)
    {
        if (null !== $isCredentialsNonExpired) {
            $this->isCredentialsNonExpired = $isCredentialsNonExpired;
        }
        return $this->isCredentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled($isEnabled = null)
    {
        if (null !== $isEnabled) {
            $this->isEnabled = $isEnabled;
        }
        return $this->isEnabled;
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
    public function setPassword($password)
    {
        $this->password = $password;
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
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        if (is_callable($this->roles)) {
            // roles lazy loader specified
            $this->roles = call_user_func($this->roles, $this);
        }
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles($roles)
    {
        $this->isRolesDirt(true);
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * {@inheritdoc}
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordEncoded($isPasswordEncoded = null)
    {
        if (null !== $isPasswordEncoded) {
            $this->isPasswordEncoded = $isPasswordEncoded;
        }
        return $this->isPasswordEncoded;
    }

    /**
     * {@inheritdoc}
     */
    public function isRolesDirt($isRolesDirt = null)
    {
        if (null !== $isRolesDirt) {
            $this->isRolesDirt = $isRolesDirt;
        }
        return $this->isRolesDirt;
    }

    function __sleep()
    {
        $this->getRoles();
        return array_keys(get_object_vars($this));
    }


} 
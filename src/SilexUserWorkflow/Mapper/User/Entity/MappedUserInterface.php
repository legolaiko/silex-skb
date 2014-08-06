<?php

namespace SilexUserWorkflow\Mapper\User\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface MappedUserInterface extends AdvancedUserInterface
{
    public function getId();
    public function setId($id);

    public function getNickname();
    public function setNickname($nickname);

    public function isPasswordEncoded($isPasswordEncoded = null);
    public function isRolesDirt($isRolesDirt = null);

    // Setters for AdvancedUserInterface



    public function isAccountNonExpired($isAccountNonExpired = null);
    public function isAccountNonLocked($isAccountNonLocked = null);
    public function isCredentialsNonExpired($isCredentialsNonExpired = null);
    public function isEnabled($isEnabled = null);

    public function setUsername($username);
    public function setPassword($password);
    public function setSalt($salt);

    /**
     * Sets user roles
     *
     * @param array|callable $roles An array of roles or callable for resolving this array
     * @return mixed
     */
    public function setRoles($roles);
} 
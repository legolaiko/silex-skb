<?php


namespace SilexUserWorkflow\Test\Mapper\User;


use SilexUserWorkflow\Mapper\User\Entity\User;

class UserEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testProperties()
    {
        $user = new User();

        $user->setUsername('test_username');
        $user->setPassword('test_password');
        $user->setSalt('test_salt');
        $user->setRoles(['test_role']);
        $user->setId('1');
        $user->setNickname('test_nickname');

        $user->isPasswordEncoded(true);
        $user->isEnabled(false);
        $user->isAccountNonExpired(true);
        $user->isAccountNonLocked(false);
        $user->isCredentialsNonExpired(true);

        $this->assertEquals('test_username', $user->getUsername());
        $this->assertEquals('test_password', $user->getPassword());
        $this->assertEquals('test_salt', $user->getSalt());
        $this->assertEquals(['test_role'], $user->getRoles());
        $this->assertEquals('1', $user->getId());
        $this->assertEquals('test_nickname', $user->getNickname());
        $this->assertEquals(true, $user->isPasswordEncoded());
        $this->assertEquals(false, $user->isEnabled());
        $this->assertEquals(true, $user->isAccountNonExpired());
        $this->assertEquals(false, $user->isAccountNonLocked());
        $this->assertEquals(true, $user->isCredentialsNonExpired());


        // invert flags

        $user->isPasswordEncoded(false);
        $user->isEnabled(true);
        $user->isAccountNonExpired(false);
        $user->isAccountNonLocked(true);
        $user->isCredentialsNonExpired(false);

        $this->assertEquals(false, $user->isPasswordEncoded());
        $this->assertEquals(true, $user->isEnabled());
        $this->assertEquals(false, $user->isAccountNonExpired());
        $this->assertEquals(true, $user->isAccountNonLocked());
        $this->assertEquals(false, $user->isCredentialsNonExpired());

    }

    public function testSetRolesCallable()
    {
        $user = new User();

        $user->setRoles(function() {
            return ['test_role1'];
        });

        $this->assertEquals(['test_role1'], $user->getRoles());
    }

    public function testAutoIsRolesDirt()
    {
        $user = new User();
        $user->isRolesDirt(false);
        $user->setRoles(['test_role']);

        $this->assertEquals(true, $user->isRolesDirt());
    }

    public function testEraseCredentials()
    {
        $user = new User();
        $user->setPassword('test_password');
        $user->setSalt('test_salt');

        $user->eraseCredentials();

        $this->assertNull($user->getPassword());
        $this->assertNull($user->getSalt());
    }

}
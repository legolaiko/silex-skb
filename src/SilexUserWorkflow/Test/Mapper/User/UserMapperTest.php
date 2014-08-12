<?php


namespace SilexUserWorkflow\Test\Mapper\User;


use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;
use SilexUserWorkflow\Mapper\User\UserMapper;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Role\Role;

class UserMapperTest extends \PHPUnit_Framework_TestCase
{

    private $fieldMap;

    protected function setUp()
    {
        $this->fieldMap = [
            MappedUserInterface::FIELD_ID       => 'id',
            MappedUserInterface::FIELD_USERNAME => 'uname'
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Field map must contain required keys: <id>, <username>
     */
    public function testConstructInvalidFieldMap()
    {
        $fieldMap = [ ];
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');

        // throws exception
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');
    }

    public function testConstructValid()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Instance of given class <\SilexUserWorkflow\Test\Mapper\User\Mock\MockInvalidUser> must implement MappedUserInterface
     */
    public function testCreateInvalidClass()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Test\Mapper\User\Mock\MockInvalidUser');

        // throws exception
        $user = $mapper->create();
    }

    public function testCreateCustomClass()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            ['myField' => 'test_val'], '\SilexUserWorkflow\Test\Mapper\User\Mock\MockCustomUser');

        $user = $mapper->create();
        $this->assertEquals('test_val', $user->getMyField());
    }

    public function testFindByUsername()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');

        $adapter->expects($this->once())
            ->method('findUser')
            ->with($this->equalTo(['uname' => 'test_username']))
            ->willReturn(['id' => 1, 'uname' => 'test_username']);

        $user = $mapper->findByUsername('test_username');
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('test_username', $user->getUsername());

        return $user;
    }

    public function testSaveUserInsert()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');

        $adapter->expects($this->once())
            ->method('insertUser')
            ->with($this->equalTo(['uname' => 'test_username']))
            ->willReturn(1); // new user id returned

        $user = $mapper->create();
        $user->setUsername('test_username');

        $mapper->save($user);
        $this->assertEquals(1, $user->getId());
    }

    /**
     * @depends testFindByUsername
     */
    public function testSaveUserUpdate(MappedUserInterface $user)
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');

        $adapter->expects($this->once())
            ->method('updateUser')
            ->with(
                $this->equalTo(['id' => 1]),
                $this->equalTo(['uname' => 'new_test_username'])
            );

        $user->setUsername('new_test_username');
        $mapper->save($user);
    }

    public function testLoadRoles()
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');

        $adapter->expects($this->once())
            ->method('findUser')
            ->with($this->equalTo(['uname' => 'test_username']))
            ->willReturn(['id' => 1, 'uname' => 'test_username']);

        $adapter->expects($this->once())
            ->method('findUserRoles')
            ->with($this->equalTo(1))
            ->willReturn(['TEST_ROLE_1', 'TEST_ROLE_2']);

        $user = $mapper->findByUsername('test_username');

        $roles = $user->getRoles();
        $this->assertCount(2, $roles);
        $this->assertEquals('TEST_ROLE_1', $roles[0]->getRole());
        $this->assertEquals('TEST_ROLE_2', $roles[1]->getRole());
    }

    /**
     * @depends testFindByUsername
     */
    public function testSaveRoles(MappedUserInterface $user)
    {
        $adapter = $this->getMock('\SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface');
        $mapper = new UserMapper($adapter, new PropertyAccessor(false, true), $this->fieldMap,
            [], '\SilexUserWorkflow\Mapper\User\Entity\User');

        $user->setRoles([
            new Role('TEST_ROLE_1'),
            new Role('TEST_ROLE_2')
        ]);

        $adapter->expects($this->once())
            ->method('replaceUserRoles')
            ->with(
                $this->equalTo(1),
                $this->equalTo(['TEST_ROLE_1', 'TEST_ROLE_2'])
            );

        $mapper->save($user);
    }
}
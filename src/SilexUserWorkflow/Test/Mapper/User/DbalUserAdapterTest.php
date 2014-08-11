<?php


namespace SilexUserWorkflow\Test\Mapper\User;


use SilexUserWorkflow\Mapper\User\Adapter\Dbal\Adapter;

class DbalUserAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testOptions()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn, ['usersTableName' => 'my_test_users']);

        $this->assertEquals('my_test_users', $adapter->getOptions()['usersTableName']);
    }

    public function testInsertUser()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $conn->expects($this->once())
            ->method('insert')
            ->with(
                $this->equalTo('user__user'), // default table name
                $this->equalTo(['test_col_1' => 'test_val_1', 'test_col_2' => 'test_val_2'])
            );

        $conn->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(42);

        $userId = $adapter->insertUser(['test_col_1' => 'test_val_1', 'test_col_2' => 'test_val_2']);

        $this->assertEquals(42, $userId);
    }

    public function testUpdateUser()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $conn->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo('user__user'), // default table name
                $this->equalTo(['test_col_1' => 'test_val_1', 'test_col_2' => 'test_val_2']),
                $this->equalTo(['id' => 42])
            );

        $adapter->updateUser(['id' => 42], ['test_col_1' => 'test_val_1', 'test_col_2' => 'test_val_2']);
    }

    public function testDeleteUser()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $conn->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo('user__user'), // default table name
                $this->equalTo(['id' => 42])
            );

        $adapter->deleteUser(['id' => 42]);
    }

    public function testFindUser()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $conn->expects($this->once())
            ->method('fetchArray')
            ->with(
                $this->equalTo('SELECT * FROM user__user WHERE test_col_1 = ? AND test_col_2 = ?'),
                $this->equalTo(['test_val_1', 'test_val_2'])
            )
            ->willReturn(['test_user' => 'test_user']);

        $user = $adapter->findUser(['test_col_1' => 'test_val_1', 'test_col_2' => 'test_val_2']);

        $this->assertEquals(['test_user' => 'test_user'], $user);
    }

    public function testFindUserRoles()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $conn->expects($this->once())
            ->method('fetchAll')
            ->with(
                $this->anything(),
                $this->equalTo([42])
            )
            ->willReturn(['TEST_ROLE_1', 'TEST_ROLE_2']);

        $roles = $adapter->findUserRoles(42);
        $this->assertEquals(['TEST_ROLE_1', 'TEST_ROLE_2'], $roles);
    }

    public function testReplaceUserRoles()
    {
        $conn = $this->getMockBuilder('\Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new Adapter($conn);

        $adapter->replaceUserRoles(42, ['TEST_ROLE_1', 'TEST_ROLE_2']);

        // TODO assertion
    }
}
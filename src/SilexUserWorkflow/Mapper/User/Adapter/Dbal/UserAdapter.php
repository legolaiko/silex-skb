<?php


namespace SilexUserWorkflow\Mapper\User\Adapter\Dbal;


use Doctrine\DBAL\Connection;
use SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdapter implements UserAdapterInterface
{
    protected $defaultOptions = [
        'usersTableName'     => 'user',
        'rolesTableName'     => 'role',
        'adjacencyTableName' => 'role_to_user',
        'tablePrefix'        => 'user__',
        'roleNameCol'        => 'role',
        'roleIdCol'          => 'id',
        'adjRoleIdCol'       => 'role_id',
        'adjUserIdCol'       => 'user_id'
    ];

    protected $options;
    protected $conn;

    public function __construct(Connection $conn, array $options = [])
    {
        $this->conn = $conn;
        $this->setupOptions($options);
    }

    public function insertUser(array $data)
    {
        $usersTable = $this->options['tablePrefix'] . $this->options['usersTableName'];
        $this->conn->insert($usersTable, $data);
        return $this->conn->lastInsertId();
    }

    public function findUser(array $criteria)
    {
        $usersTable = $this->options['tablePrefix'] . $this->options['usersTableName'];
        $where = implode(' = ? AND ', array_keys($criteria)) . ' = ?';

        return $this->conn->fetchAssoc(
            'SELECT * FROM ' . $usersTable . ' WHERE ' . $where, array_values($criteria)
        );
    }

    public function updateUser(array $criteria, array $data)
    {
        $usersTable = $this->options['tablePrefix'] . $this->options['usersTableName'];
        $this->conn->update($usersTable, $data, $criteria);
    }

    public function deleteUser(array $criteria)
    {
        $usersTable = $this->options['tablePrefix'] . $this->options['usersTableName'];
        $this->conn->delete($usersTable, $criteria);
    }

    public function findUserRoles($userId)
    {
        $rolesTable = $this->options['tablePrefix'] . $this->options['rolesTableName'];
        $adjTable   = $this->options['tablePrefix'] . $this->options['adjacencyTableName'];
        $sql = "SELECT r.{$this->options['roleNameCol']} FROM $rolesTable r, $adjTable ra"
            .  "  WHERE ra.{$this->options['adjRoleIdCol']} = r.{$this->options['roleIdCol']}"
            .  "    AND ra.{$this->options['adjUserIdCol']} = ? ";

        $roles = $this->conn->fetchAll($sql, [$userId]);
        array_walk($roles, function(&$item){
            $item = $item[$this->options['roleNameCol']]; // fetching first column
        });

        return $roles;
    }

    public function replaceUserRoles($userId, array $roles)
    {
        $rolesTable = $this->options['tablePrefix'] . $this->options['rolesTableName'];
        $adjTable   = $this->options['tablePrefix'] . $this->options['adjacencyTableName'];

        $this->conn->beginTransaction();

        $this->conn->delete($adjTable, [$this->options['adjUserIdCol'] => $userId]);
        if (!empty($roles)) {
            $sql = "INSERT INTO $adjTable ({$this->options['adjUserIdCol']}, {$this->options['adjRoleIdCol']})"
                .  "  SELECT $userId, {$this->options['roleIdCol']} FROM $rolesTable"
                .  "    WHERE {$this->options['roleNameCol']} IN (?)";
            $this->conn->executeQuery($sql, [$roles], [Connection::PARAM_INT_ARRAY]);
        }

        $this->conn->commit();
    }


    public function getOptions()
    {
        return $this->options;
    }

    protected function setupOptions(array $options)
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefaults($this->defaultOptions);
        $this->options = $optionsResolver->resolve($options);
    }
} 
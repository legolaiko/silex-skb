<?php


namespace User\UserMapper;
use Doctrine\DBAL\Connection;
use User\User;


class UserMapper
{
    protected $dbConn;
    protected $userRolesMap;

    public function __construct(Connection $dbConn)
    {
        $this->dbConn = $dbConn;
    }

    public function insertUser(User $user)
    {
        $this->dbConn->insert(
            'user', [
                'username' => $user->getUsername(),
                'password' => $user->getPassword()
            ]
        );

        $user->setId($this->dbConn->lastInsertId());

        $this->saveUserRoles($user);
    }

    /**
     * @param $username
     * @return bool|User false on failure
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByUsername($username)
    {
        $stmt = $this->dbConn->executeQuery(
            'SELECT * FROM user WHERE username = ?', [$username]
        );

        $user = $stmt->fetch();

        if ($user) {
            $user = (new User())
                ->setUsername($user['username'])
                ->setPassword($user['password']);

            $this->loadUserRoles($user);
        }

        return $user;
    }

    protected function saveUserRoles(User $user)
    {
        $this->dbConn->delete('user_to_user_role', [
           'user_id' => $user->getId()
        ]);

        foreach ($user->getRoles() as $role) {
            if (!array_key_exists($role, $this->getRolesMap())) {
                throw new \UnexpectedValueException('Unexpected role \'' . $role . '\'');
            }
            $this->dbConn->insert('user_to_user_role', [
                'user_id' => $user->getId(),
                'role_id' => $this->getRolesMap()[$role]
            ]);
            // TODO: prepared statement
        }
    }

    protected function loadUserRoles(User $user)
    {
        $stmt = $this->dbConn->executeQuery(
            'SELECT ur.name FROM user_role ur, user_to_user_role utur WHERE ur.id = utur.role_id AND utur.user_id = ?',
            [$user->getId()]
        );

        $roles = [];
        while (false !== ($role = $stmt->fetch())) {
            $roles[] = $role;
        }

        $user->setRoles($roles);
    }

    protected function getRolesMap()
    {
        if (null === $this->userRolesMap) {
            $this->userRolesMap = [];
            $stmt = $this->dbConn->executeQuery('SELECT * FROM user_role');

            while (false !== ($role = $stmt->fetch())) {
                $this->userRolesMap[$role['name']] = $role['id'];
            }
        }

        return $this->userRolesMap;
    }
} 
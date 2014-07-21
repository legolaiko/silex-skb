<?php


namespace User\UserMapper;
use Doctrine\DBAL\Connection;
use User\User;


class UserMapper
{
    protected $dbConn;

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
    }

    /**
     * @param $username
     * @return bool|User false on failure
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByUsername($username)
    {
        $stmt = $this->dbConn->executeQuery(
            'SELECT * FROM user WHERE username = ?', $username
        );

        $user = $stmt->fetch();

        if ($user) {
            $user = (new User())
                ->setUsername($user['username'])
                ->setPassword($user['password'])
                ->setRoles(['ROLE_USER']);
        }

        return $user;
    }
} 
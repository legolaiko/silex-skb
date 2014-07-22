<?php


namespace User\UserMapper;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use User\User;
use User\UserFactory\UserFactoryInterface;


class UserMapperDbal implements UserMapperInterface
{
    protected $userFactory;
    protected $dbConn;
    protected $userRolesMap;

    public function __construct(UserFactoryInterface $userFactory, Connection $dbConn)
    {
        $this->userFactory = $userFactory;
        $this->dbConn      = $dbConn;
    }

    public function insertUser(UserInterface $user)
    {
        $this->assertUser($user);
        /* @var $user UserDbalInterface */

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
     * @return bool|UserDbalInterface false on failure
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByUsername($username)
    {
        $stmt = $this->dbConn->executeQuery(
            'SELECT * FROM user WHERE username = ?', [$username]
        );

        $user = $userData = $stmt->fetch();

        if ($userData) {
            $user = $this->userFactory->createUser();
            $this->assertUser($user);
            /* @var $user UserDbalInterface */

            $user->setId($userData['id']);
            $user->setUsername($userData['username']);
            $user->setPassword($userData['password']);
            $this->loadUserRoles($userData);
        }

        return $user;
    }

    /**
     * Gets UserFactoryInterface associated with mapper
     *
     * @return UserFactoryInterface
     */
    public function getUserFactory()
    {
        return $this->userFactory;
    }


    protected function assertUser(UserInterface $user)
    {
        if (!($user instanceof UserDbalInterface)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
    }

    protected function saveUserRoles(UserDbalInterface $user)
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

    protected function loadUserRoles(UserDbalInterface $user)
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
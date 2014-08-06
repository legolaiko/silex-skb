<?php


namespace SilexUserWorkflow\UserManager\Dbal;


use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use SilexUserWorkflow\UserManager\UserManagerInterface;


class UserDbalManager implements UserManagerInterface
{
    protected $userClass;
    protected $userSalt;
    protected $conn;
    protected $encoderFactory;

    protected $userRolesMap;

    public function __construct($userClass, $userSalt, Connection $conn, EncoderFactoryInterface $encoderFactory)
    {
        $this->userClass      = $userClass;
        $this->userSalt       = $userSalt;
        $this->conn           = $conn;
        $this->encoderFactory = $encoderFactory;
    }

    public function createUser()
    {
        $user = new $this->userClass;
        $this->assertUser($user);
        /* @var $user UserDbalInterface */
        $user->setSalt($this->userSalt);
        $user->setRoles(['ROLE_USER']);
        return $user;
    }

    public function insertUser(UserInterface $user)
    {
        $this->assertUser($user);
        /* @var $user UserDbalInterface */

        $this->ensurePasswordEncoded($user);
        $this->conn->insert(
            'user', $this->dumpToArray($user)
        );

        $user->setId($this->conn->lastInsertId());
        $this->saveUserRoles($user);
    }

    /**
     * Updates existing user in storage
     *
     * @param UserInterface $user
     * @return void
     */
    public function updateUser(UserInterface $user)
    {
        $this->assertUser($user);
        /* @var $user UserDbalInterface */

        $this->ensurePasswordEncoded($user);
        $this->conn->update(
            'user', $this->dumpToArray($user), ['id' => $user->getId()]
        );

        $this->saveUserRoles($user);
    }


    /**
     * @param $username
     * @return bool|UserDbalInterface false on failure
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByUsername($username)
    {
        $stmt = $this->conn->executeQuery(
            'SELECT * FROM user WHERE username = ?', [$username]
        );

        $user = $userData = $stmt->fetch();

        if ($userData) {
            $user = $this->createUser();
            $this->initFromArray($user, $userData)
                ->loadUserRoles($user);
        }

        return $user;
    }

    protected function initFromArray(UserDbalInterface $user, $userData)
    {
        $user->setId($userData['id']);
        $user->setUsername($userData['username']);
        $user->setPassword(new PasswordEncoded($userData['password']));
        $user->setNickname($userData['nickname']);
        $user->setEnabled($userData['enabled']);

        return $this;
    }

    protected function dumpToArray(UserDbalInterface $user)
    {
        return [
            'username' => $user->getUsername(),
            'password' => (string)$user->getPassword(),
            'nickname' => $user->getNickname(),
            'enabled'  => $user->isEnabled()
        ];
    }

    protected function ensurePasswordEncoded(UserDbalInterface $user)
    {
        if (!($user->getPassword() instanceof PasswordEncoded)) {
            $pwdEncoded = $this->encoderFactory
                ->getEncoder($user)->encodePassword($user->getPassword(), $user->getSalt());
            $pwdEncoded = new PasswordEncoded($pwdEncoded);
            $user->setPassword($pwdEncoded);
        }

        return $this;
    }

    protected function assertUser($user)
    {
        if (!($user instanceof UserDbalInterface)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
    }

    protected function saveUserRoles(UserDbalInterface $user)
    {
        $this->conn->delete('user_to_user_role', [
           'user_id' => $user->getId()
        ]);

        foreach ($user->getRoles() as $role) {
            if ($role instanceof RoleInterface) {
                $role = $role->getRole();
            }
            /** @var string $role */
            if (!array_key_exists($role, $this->getRolesMap())) {
                throw new \UnexpectedValueException('Unexpected role \'' . $role . '\'');
            }
            $this->conn->insert('user_to_user_role', [
                'user_id' => $user->getId(),
                'role_id' => $this->getRolesMap()[$role]
            ]);
            // TODO: prepared statement
        }
    }

    protected function loadUserRoles(UserDbalInterface $user)
    {
        $stmt = $this->conn->executeQuery(
            'SELECT ur.name FROM user_role ur, user_to_user_role utur WHERE ur.id = utur.role_id AND utur.user_id = ?',
            [$user->getId()]
        );

        $roles = [];
        while (false !== ($role = $stmt->fetch())) {
            $roles[] = $role['name'];
        }

        $user->setRoles($roles);
    }

    protected function getRolesMap()
    {
        if (null === $this->userRolesMap) {
            $this->userRolesMap = [];
            $stmt = $this->conn->executeQuery('SELECT * FROM user_role');

            while (false !== ($role = $stmt->fetch())) {
                $this->userRolesMap[$role['name']] = $role['id'];
            }
        }

        return $this->userRolesMap;
    }
} 
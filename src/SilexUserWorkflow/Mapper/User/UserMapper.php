<?php


namespace SilexUserWorkflow\Mapper\User;


use SilexUserWorkflow\Mapper\User\Adapter\UserAdapterInterface;
use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\Role\Role;

class UserMapper implements UserMapperInterface
{
    protected $adapter;
    protected $accessor;
    protected $fieldMap;
    protected $defaults;
    protected $userClass;


    public function __construct(UserAdapterInterface $adapter, PropertyAccessorInterface $accessor,
                                array $fieldMap, array $defaults, $userClass)
    {
        $this->adapter   = $adapter;
        $this->accessor  = $accessor;
        $this->userClass = $userClass;
        $this->defaults  = $defaults;
        $this->setFieldMap($fieldMap);
    }


    /**
     * Creates new user, factory method
     *
     * @return MappedUserInterface
     */
    public function create()
    {
        $user = new $this->userClass;
        if (!($user instanceof MappedUserInterface)) {
            throw new \LogicException(
                sprintf('Instance of given class <%s> must implement MappedUserInterface', $this->userClass)
            );
        }
        $this->setupUserDefaults($user);
        return $user;
    }

    /**
     * Saves (inserts new or updates existing one) user to storage
     *
     * @param MappedUserInterface $user
     * @return void
     */
    public function save(MappedUserInterface $user)
    {
        $mappedUser = $this->mapUserToArray($user);
        $idCol = $this->fieldMap[MappedUserInterface::FIELD_ID];

        unset($mappedUser[$idCol]);

        if (null !== $user->getId()) {
            $this->adapter->updateUser(
                [$idCol => $user->getId()],
                $mappedUser
            );
        } else {
            $id = $this->adapter->insertUser($mappedUser);
            $user->setId($id);
        }

        $this->saveUserRoles($user);
    }

    /**
     * Finds user in storage by username
     *
     * @param $username
     * @return MappedUserInterface|false
     */
    public function findByUsername($username)
    {
        $usernameCol = $this->fieldMap[MappedUserInterface::FIELD_USERNAME];
        return $this->findUser([$usernameCol => $username]);
    }

    public function findUserRoles(MappedUserInterface $user)
    {
        $rolesStr = $this->adapter->findUserRoles($user->getId());
        return $this->mapRoles($rolesStr);
    }

    protected function findUser(array $criteria)
    {
        $user = $this->adapter->findUser($criteria);
        if ($user) {
            $user = $this->mapArrayToUser($user);
            $this->loadUserRoles($user);
        }
        return $user;
    }

    protected function setupUserDefaults(MappedUserInterface $user)
    {
        foreach($this->defaults as $fieldName => $defaultValue) {
            if (MappedUserInterface::FIELD_ROLES === $fieldName) {
                // roles have to be mapped before
                $defaultValue = $this->mapRoles($defaultValue);
            }
            $this->accessor->setValue($user, $fieldName, $defaultValue);
        }
    }

    protected function mapRoles(array $rolesStr)
    {
        $roles = [];
        foreach ($rolesStr as $roleStr) {
            $roles[] = new Role($roleStr);
        }
        return $roles;
    }

    protected function mapUserToArray(MappedUserInterface $user)
    {
        $mappedUser = [];
        foreach ($this->fieldMap as $fieldName => $colName) {
            $value = $this->accessor->getValue($user, $fieldName);
            $mappedUser[$colName] = $value;
        }
        return $mappedUser;
    }

    protected function mapArrayToUser(array $userData)
    {
        $mappedUser = $this->create();
        foreach ($this->fieldMap as $fieldName => $colName) {
            if (array_key_exists($colName, $userData)) {
                $this->accessor->setValue($mappedUser, $fieldName, $userData[$colName]);
            }
        }
        return $mappedUser;
    }

    protected function loadUserRoles(MappedUserInterface $user)
    {
        $user->setRoles([$this, 'findUserRoles']);
        $user->isRolesDirt(false);
    }

    protected function saveUserRoles(MappedUserInterface $user)
    {
        if ($user->isRolesDirt()) {
            $rolesStr = [];
            foreach ($user->getRoles() as $role) {
                $rolesStr[] = $role->getRole();
            }
            $this->adapter->replaceUserRoles($user->getId(), $rolesStr);
            $user->isRolesDirt(false);
        }
    }

    protected function setFieldMap($fieldMap)
    {
        $this->fieldMap = $fieldMap;

        // validating required fields in field map
        if (!isset($fieldMap[MappedUserInterface::FIELD_ID])
            || !isset($fieldMap[MappedUserInterface::FIELD_USERNAME])) {
            throw new \InvalidArgumentException(
                sprintf('Field map must contain required keys: <%s>, <%s>'
                    , MappedUserInterface::FIELD_ID, MappedUserInterface::FIELD_USERNAME)
            );
        }
    }

} 
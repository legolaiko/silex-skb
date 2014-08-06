<?php


namespace SilexUserWorkflow\Mapper\User;


use SilexUserWorkflow\Mapper\User\Adapter\AdapterInterface;
use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Mapper implements MapperInterface
{
    const FIELD_ID       = 'id';
    const FIELD_USERNAME = 'username';

    protected $adapter;
    protected $accessor;
    protected $userClass;
    protected $salt;

    protected $fieldMap;
    protected $fieldMapFlipped;

    public function __construct(
        AdapterInterface $adapter, PropertyAccessorInterface $accessor, array $fieldMap, $userClass, $salt
    )
    {
        $this->adapter   = $adapter;
        $this->accessor  = $accessor;
        $this->userClass = $userClass;
        $this->salt      = $salt;

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
        // TODO: Implement save() method.
    }

    /**
     * Finds user in storage by username
     *
     * @param $username
     * @return MappedUserInterface
     */
    public function findByUsername($username)
    {
        // TODO: Implement findByUsername() method.
    }

    protected function setupUserDefaults(MappedUserInterface $user)
    {
        $user->setSalt($this->salt);
    }

    protected function setFieldMap($fieldMap)
    {
        $this->fieldMap        = $fieldMap;
        $this->fieldMapFlipped = array_flip($fieldMap);


    }

} 
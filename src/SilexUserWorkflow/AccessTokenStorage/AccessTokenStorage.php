<?php


namespace SilexUserWorkflow\AccessTokenStorage;


use SilexUserWorkflow\AccessTokenStorage\Adapter\StorageAdapterInterface;
use SilexUserWorkflow\AccessTokenStorage\Generator\TokenGeneratorInterface;

class AccessTokenStorage implements AccessTokenStorageInterface
{
    protected $generator;
    protected $adapter;
    protected $insertionAttempts;

    public function __construct(
        TokenGeneratorInterface $generator, StorageAdapterInterface $adapter, $insertionAttempts = 10
    )
    {
        $this->generator         = $generator;
        $this->adapter           = $adapter;
        $this->insertionAttempts = $insertionAttempts;
    }

    public function createData($data)
    {
        $data = serialize($data);

        $result = false;
        $token  = null;

        for($i = 1; $i <= $this->insertionAttempts; $i++) {
            $token  = $this->generator->generateToken();
            $result = $this->adapter->createUnique($token, $data);
            if ($result) {
                break;
            }
        }
        if (!$result) {
            throw new \RuntimeException('Can\'t insert token to storage: to many attempts');
        }

        return $token;
    }

    public function readData($accessToken)
    {
        $data = $this->adapter->read($accessToken);
        if ($accessToken) {
            $data = unserialize($data);
        }
        return $data;
    }


    public function updateData($accessToken, $data)
    {
        $data = serialize($data);
        $this->adapter->update($accessToken, $data);
    }

    public function deleteData($accessToken)
    {
        $this->adapter->delete($accessToken);
    }


} 
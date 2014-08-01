<?php


namespace User\AccessTokenStorage;


use User\AccessTokenStorage\Adapter\StorageAdapterInterface;

class AccessTokenStorage implements AccessTokenStorageInterface
{
    protected $generator;
    protected $adapter;
    protected $insertionAttempts;

    public function __construct(
        AccessTokenGeneratorInterface $generator, StorageAdapterInterface $adapter, $insertionAttempts = 10
    )
    {
        $this->generator         = $generator;
        $this->adapter           = $adapter;
        $this->insertionAttempts = $insertionAttempts;
    }

    /**
     * Puts data to storage
     *
     * @param mixed $data Data to store
     * @return string Access token
     */
    public function putData($data)
    {
        $data = serialize($data);

        $result = false;
        $token  = null;

        for($i = 1; $i < $this->insertionAttempts; $i++) {
            $token = $this->generator->generateToken();
            $result = $this->adapter->insert([
                StorageAdapterInterface::FIELD_TOKEN => $token,
                StorageAdapterInterface::FIELD_DATA  => $data
            ]);
            if ($result) {
                break;
            }
        }
        if (!$result) {
            throw new \RuntimeException('Can\'t insert token to storage: to many attempts');
        }

        return $token;
    }

    /**
     * Gets data from storage
     *
     * @param string $accessToken Unique access token, given by @see putData
     * @return mixed Stored data
     */
    public function getData($accessToken)
    {
        $data = $this->adapter->findByToken($accessToken);
        if ($accessToken) {
            $data = unserialize($data[StorageAdapterInterface::FIELD_DATA]);
        }
        return $data;
    }

} 
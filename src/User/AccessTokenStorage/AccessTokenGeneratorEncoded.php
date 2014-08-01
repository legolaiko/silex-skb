<?php


namespace User\AccessTokenStorage;


use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class AccessTokenGeneratorEncoded implements  AccessTokenGeneratorInterface
{

    protected $passwordEncoder;
    protected $salt;

    public function __construct(PasswordEncoderInterface $passwordEncoder, $salt)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->salt            = $salt;
    }

    /**
     * Generates unique and secured access token
     *
     * @return string
     */
    public function generateToken()
    {
        $token = uniqid('', true);
        $token = $this->passwordEncoder->encodePassword($token, $this->salt);
        return $token;
    }

} 
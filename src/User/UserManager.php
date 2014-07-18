<?php


namespace User;

use Doctrine\DBAL\Connection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserManager
{
    protected $formFactory;
    protected $encoderFactory;
    protected $dbConn;
    protected $salt;

    function __construct(
        FormFactoryInterface $formFactory,
        EncoderFactoryInterface $encoderFactory,
        Connection $dbConn,
        $salt = 'ladkfn34')
    {
        $this->formFactory    = $formFactory;
        $this->encoderFactory = $encoderFactory;
        $this->dbConn         = $dbConn;
        $this->salt           = $salt;
    }

    public function createLoginForm()
    {
        $form = $this->formFactory
            ->createBuilder('form')
            ->add('email', 'text')
            ->add('password', 'password')
            ->add('rememberMe', 'checkbox', ['required' => false])
            ->add('signIn', 'submit')
            ->getForm();

        return $form;
    }

    public function createRegisterForm()
    {
        $user = new User();
        $user->setSalt($this->salt);

        $form = $this->formFactory
            ->createBuilder('form', $user)
            ->add('username', 'email', [
                'constraints' => [new Email(), new Length(['min' => 4, 'max' => 128])],
                'label' => 'Email'
            ])
            ->add('password', 'repeated', [
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'   => ['label' => 'Password'],
                'second_options'  => ['label' => 'Repeat password'],
                'constraints' => new Length(['min' => 4, 'max' => 64])
            ])
            ->add('signUp', 'submit')
            ->getForm();
        return $form;
    }


    public function registerUser(User $user, $isPasswordEncoded = false)
    {
        if (!$isPasswordEncoded) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $pwdEncoded = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($pwdEncoded);
        }

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
            'SELECT * FROM users WHERE username = ?', array(strtolower($username))
        );

        $user = $stmt->fetch();

        if ($user) {
            $user = new User();
            $user->setUsername($user['username'])
                 ->setPassword($user['password']);
        }

        return $user;
    }
} 
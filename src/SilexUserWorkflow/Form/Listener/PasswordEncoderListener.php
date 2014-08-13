<?php


namespace SilexUserWorkflow\Form\Listener;


use SilexUserWorkflow\Mapper\User\Entity\MappedUserInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PasswordEncoderListener
{
    protected $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    function __invoke(FormEvent $event)
    {
        $user = $event->getData();
        if (!($user instanceof MappedUserInterface)) {
            throw new \InvalidArgumentException('User must implement `MappedUserInterface`');
        }

        $encoder         = $this->encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());

        $user->setPassword($encodedPassword);
    }

} 
<?php


namespace User\UserController;

use Symfony\Component\Security\Core\SecurityContextInterface;
use User\ViewRenderer\RendererInterface;

class ProfileController
{
    protected $security;
    protected $renderer;

    public function __construct(SecurityContextInterface $security, RendererInterface $renderer)
    {
        $this->security = $security;
        $this->renderer = $renderer;
    }

    public function handle()
    {
        $user = $this->security->getToken();
        if (null === $user) {
            throw new \LogicException('Token must not be null');
        }
        $user = $user->getUser();

        $avatarUrl = "http://www.gravatar.com/avatar/"
            . md5(strtolower(trim($user->getUsername())))
            . "&s=80";

        return $this->renderer->render('user/profile', ['user' => $user, 'avatarUrl' => $avatarUrl]);
    }
}
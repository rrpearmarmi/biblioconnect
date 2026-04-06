<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
        } elseif (in_array('ROLE_LIBRARIAN', $roles, true)) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_librarian_dashboard'));
        } else {
            $response = new RedirectResponse($this->urlGenerator->generate('app_user_dashboard'));
        }

        $event->setResponse($response);
    }
}

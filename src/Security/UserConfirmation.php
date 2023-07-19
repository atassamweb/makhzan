<?php

namespace App\Security;

use App\Entity\User;
use DateTimeInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserConfirmation implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$user->isIsVerified() && $user->getTokenRegistrationLifeTime() instanceof DateTimeInterface) {
            $formattedDate = $user->getTokenRegistrationLifeTime()->format('d/m/y à H\hi');
            throw new CustomUserMessageAccountStatusException("Votre compte n'est pas vérifié,
             merci de le confirmer avant le {$formattedDate}.");
        }
    }
}

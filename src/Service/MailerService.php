<?php

namespace  App\Service;

use http\Exception\InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service qui permet de générer un email
 */
class MailerService
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function send(string $to, string $subject, string $templateTwig, array $context): void
    {


        $email = (new TemplatedEmail())
            ->from(new Address('noreply@monsite.td', 'monsite'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("mails/$templateTwig")
            ->context($context);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $transportException) {
            throw $transportException;
        }
    }
}

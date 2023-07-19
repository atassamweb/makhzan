<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerService $mailerService,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Token
            $tokenRegistration = $tokenGenerator->generateToken();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //USER TOKEN
            $user->setTokenRegistration($tokenRegistration);

            $entityManager->persist($user);
            $entityManager->flush();

            // MAILER SEND
            $to = $user->getEmail();
            $lifeTimeToken = $user->getTokenRegistrationLifeTime();
            $lifeTimeTokenFormatted = '';
            if ($lifeTimeToken instanceof DateTimeInterface) {
                $lifeTimeTokenFormatted = $lifeTimeToken->format('d/m/y à H\hi');
            }
            if ($to !== null) {
                $mailerService->send(
                    $to,
                    'Confirmation du compte utilisateur',
                    'registration_confirmation.html.twig',
                    [
                        'user' => $user,
                        'token' => $tokenRegistration,
                        'lifeTimeToken' => $lifeTimeTokenFormatted
                    ]
                );


                $this->addFlash('success', 'Votre compte a bien été créé, 
                veuillez vérifier vos e-mails pour l\'activer!');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/{token}/{id<\d+>}', name: 'account_verify', methods: ['GET'])]
    public function verify(string $token, User $user, EntityManagerInterface $entityManager): Response
    {

        if ($user->getTokenRegistration() !== $token) {
            throw new AccessDeniedException();
        }
        if ($user->getTokenRegistration() == null) {
            throw new AccessDeniedException();
        }
        if (new DateTime('now') > $user->getTokenRegistrationLifeTime()) {
            throw new AccessDeniedException();
        }
        $user->setIsVerified(true);
        $user->setTokenRegistration(null);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a bien été activé. VOus pouvez maintenant vous connecter!');

        return $this->redirectToRoute('app_login');
    }
}

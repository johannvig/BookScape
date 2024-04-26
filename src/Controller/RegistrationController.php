<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            // Génération du token de vérification
            $token = $tokenGenerator->generateToken();
            $user->setActivationToken($token);
            $user->setActivationTokenTimestamp(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            // Construction de l'URL de vérification
            $url = $this->generateUrl('verify_user', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoi de l'email de vérification
            $email = (new Email())
                ->from('johanne.vgrx@gmail.com')
                ->to($user->getEmail())
                ->subject('Activation de votre compte sur le site e-commerce')
                ->html("Pour activer votre compte, veuillez cliquer sur ce lien: " . $url . 
                "/n Attention : le lien n'est valable qu'une heure. S'il n'est plus valable, veuillez suivre quand même le lien et un nouvel email
                vous sera envoyé");

            $mailer->send($email);

            $this->addFlash('success', 'Inscription réussie. Veuillez vérifier votre email pour activer votre compte.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, UserRepository $usersRepository, EntityManagerInterface $em): Response
    {
        $user = $usersRepository->findOneByActivationToken($token);

        if (!$user) {
            $this->addFlash('danger', 'Le token est invalide');
            return $this->redirectToRoute('resend_verif');
        }

        $tokenTimestamp = $user->getActivationTokenTimestamp();
        $now = new \DateTime();

        if ($now->getTimestamp() - $tokenTimestamp->getTimestamp() > 3600) {
            // Token expiré
            $this->addFlash('danger', 'Le lien de vérification a expiré');
            return $this->redirectToRoute('renvoiverif');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Ce compte est déjà activé');
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $user->setActivationToken(null); // Réinitialiser le token pour empêcher une réutilisation
        $user->setActivationTokenTimestamp(null);

        $em->flush();

        $this->addFlash('success', 'Compte activé avec succès');
        return $this->redirectToRoute('app_login');
    }





    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_login');
        }

        // Génération d'un nouveau token de vérification
        $token = $tokenGenerator->generateToken();
        $user->setActivationToken($token);
        $user->setActivationTokenTimestamp(new \DateTime());

        $entityManager->flush();

        // Construction de l'URL de vérification
        $url = $this->generateUrl('verify_user', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // Envoi de l'email de vérification
        $email = (new Email())
            ->from('johanne.vgrx@gmail.com')
            ->to($user->getEmail())
            ->subject('Activation de votre compte sur le site e-commerce')
            ->html("Pour activer votre compte, veuillez cliquer sur ce lien: " . $url);

        $mailer->send($email);

        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('app_login');
    }

}
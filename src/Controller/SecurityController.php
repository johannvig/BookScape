<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ForgetPasswordFormType;
use App\Form\ResetPasswordFormType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                
                return $this->redirectToRoute('adminHome');
            }
            $referrerUrl = $request->headers->get('referer');
            if (strpos($referrerUrl, 'accederAuPanier') !== false) {
                return new RedirectResponse($this->router->generate('commanderPanier'));
            }
            
            return $this->redirectToRoute('userIndex');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/oubli-pass', name:'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer, // Utilisez MailerInterface ici
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailFormData = $form->get('email')->getData();
            $user = $usersRepository->findOneByEmail($emailFormData);
          

            if ($user) {
                
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $user->setResetTokenTimestamp(new \DateTime());
                $entityManager->persist($user);
                $entityManager->flush();

                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new Email())
                    ->from("johanne.vgrx@gmail.com")
                    ->to($user->getEmail())
                    ->subject("Réinitialisation de mot de passe")
                    ->html("Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien: " . $url . "/n Celui ci ne sera valable que
                    pendant 1 heure après cela vous deverez recommencer la procédure.");

                $mailer->send($email);

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }

            else{$this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('forgotten_password');}
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name:'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $usersRepository->findOneByResetToken($token);

        if ($user) {
            $tokenTimestamp = $user->getResetTokenTimestamp();
            $now = new \DateTime();
            if ($tokenTimestamp === null || $now->getTimestamp() - $tokenTimestamp->getTimestamp() > 3600) {
                // Le token a expiré
                $this->addFlash('danger', 'Le lien de réinitialisation a expiré');
                return $this->redirectToRoute('app_login');
            }

            $form = $this->createForm(ForgetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setResetToken('');
                $user->setPassword($passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ));
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }

        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}

<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande\Commande;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileController extends AbstractController
{
    #[Route('/account/index', name: 'userIndex')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/user_orders', name: 'user_orders')]
    public function orders(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        // Récupérer les commandes de l'utilisateur
        $orders = $entityManager->getRepository(Commande::class)->findBy(['user' => $user]);

        return $this->render('account/user_orders.html.twig', [
            'orders' => $orders,
        ]);
    }


    #[Route('/account/edit_profile', name: 'user_edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('userIndex');
        }

        return $this->render('account/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/change_password', name: 'account_change_password')]
    public function changePassword(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('account_change_password');
            }

            $newPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
            $user->setPassword($newPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');

            return $this->redirectToRoute('userIndex');
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

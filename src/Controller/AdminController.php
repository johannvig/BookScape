<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

use App\Entity\Commande\Commande;
use App\Entity\Catalogue\Livre;
use App\Entity\Catalogue\Musique;
use App\Form\CommandeType;
use App\Form\LivreType;
use App\Form\MusiqueType;
use App\Form\UserType;
use App\Service\NotificationService;
use App\Repository\NotificationsStockRepository;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;





class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
	private $notificationService;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, UserPasswordHasherInterface $passwordHasher, NotificationService $notificationService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->passwordHasher = $passwordHasher;
		$this->notificationService = $notificationService;
    }


    #[Route('/admin', name: 'adminHome')]
    public function adminHomeAction(): Response
    {
        return $this->render('admin/home.html.twig');
    }

    #[Route('/admin/musiques', name: 'adminMusiques')]
    public function adminMusiquesAction(Request $request): Response
    {
        $articles = $this->entityManager->getRepository(Musique::class)->findAll();
        return $this->render('admin/admin.musiques.html.twig', ['articles' => $articles]);
    }

    #[Route('/admin/livres', name: 'adminLivres')]
    public function adminLivresAction(Request $request): Response
    {
        $articles = $this->entityManager->getRepository(Livre::class)->findAll();
        return $this->render('admin/admin.livres.html.twig', ['articles' => $articles]);
    }

    #[Route('/admin/musiques/supprimer', name: 'adminMusiquesSupprimer')]
    public function adminMusiquesSupprimerAction(Request $request): Response
    {
        $id = $request->query->get("id");
        $$musique = $this->entityManager->find(Musique::class, $id);

		$this->entityManager->remove($musique);
		$this->entityManager->flush();
		return $this->redirectToRoute("adminMusiques");
	}


    #[Route('/admin/livres/supprimer', name: 'adminLivresSupprimer')]
	public function adminLivresSupprimerAction(Request $request): Response {
		$id = $request->query->get("id");
		return $this->handleDelete(Livre::class, $id, "adminLivres");
	}

    #[Route('/admin/livres/ajouter', name: 'adminLivresAjouter')]
	public function adminLivresAjouterAction(Request $request): Response {
		$livre = new Livre();
		$form = $this->handleForm($request, $livre, LivreType::class, 'adminLivres');

		if ($form instanceof Response) {
			return $form;
		}

		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}


	
    #[Route('/admin/musiques/ajouter', name: 'adminMusiquesAjouter')]
	public function adminMusiquesAjouterAction(Request $request): Response {
		$musique = new Musique();
		$form = $this->handleForm($request, $musique, MusiqueType::class, 'adminMusiques');

		if ($form instanceof Response) {
			return $form;
		}

		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}



    #[Route('/admin/musiques/modifier', name: 'adminMusiquesModifier')]
	public function adminMusiquesModifierAction(Request $request, EntityManagerInterface $entityManager, NotificationsStockRepository $notificationsStockRepository, MailerInterface $mailer): Response
	
	{
		$musiqueId = $request->query->get('id') ?: $request->request->get('id');
		$musique = $this->entityManager->getReference(Musique::class, $musiqueId);
		$ancienneDisponibilite = $musique->getDisponibilite();

		$ancienneDisponibilite = $musique->getDisponibilite();
		$form = $this->handleForm($request, $musique, MusiqueType::class, 'adminMusiques');

		if ($form instanceof Response) {
			return $form;
		}

		if ($form->isSubmitted() && $form->isValid()) {
			$this->notificationService->sendNotifications($musique, $ancienneDisponibilite);
			return $this->redirectToRoute('adminMusiques');
		}

		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}

	#[Route('/admin/livres/modifier', name: 'adminLivresModifier')]

	public function adminLivresModifier(Request $request, EntityManagerInterface $entityManager, NotificationsStockRepository $notificationsStockRepository, MailerInterface $mailer): Response
	{
		$livreId = $request->query->get("id") ?? $request->request->get("id");
		$livre = $this->entityManager->getReference(Livre::class, $livreId);

		$ancienneDisponibilite = $livre->getDisponibilite();
		$form = $this->handleForm($request, $livre, LivreType::class, 'adminLivres');
		if ($form instanceof Response) {
			return $form;
		}

		if ($form->isSubmitted() && $form->isValid()) {
			$this->notificationService->sendNotifications($livre, $ancienneDisponibilite);
			return $this->redirectToRoute('adminLivres');
		}

		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}


	
    



	#[Route('/admin/commandes', name: 'adminOrders')]
    public function adminOrders(): Response
    {
        $orders = $this->entityManager->getRepository(Commande::class)->findAll();
		return $this->render('admin/orders.html.twig', ['orders' => $orders]);

    }

	

	#[Route('/admin/users', name: 'adminUsers')]
    public function adminUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

	#[Route('/admin/orders/supprimer', name: 'adminCommandesSupprimer')]
	public function adminCommandesSupprimerAction(Request $request): Response
	{
		$id = $request->query->get("id");
		$commande = $this->entityManager->getRepository(Commande::class)->find($id);

		// Récupération des lignes de commande
		$lignesCommande = $commande->getLigneCommandes();

		foreach ($lignesCommande as $ligne) {
			// Mise à jour de la disponibilité des articles
			$article = $ligne->getArticle();
			$nouvelleDisponibilite = $article->getDisponibilite() + $ligne->getQuantite();
			$article->setDisponibilite($nouvelleDisponibilite);

			// Sauvegarder les modifications de l'article
			$this->entityManager->persist($article);
		}

		// Suppression de la commande
		$this->entityManager->remove($commande);

		// Enregistrement de toutes les modifications
		$this->entityManager->flush();

		return $this->redirectToRoute("adminOrders");
	}




	#[Route('/admin/users/supprimer', name: 'adminUtilisateursSupprimer')]
	public function adminUtilisateursSupprimerAction(Request $request): Response
	{
		$id = $request->query->get("id");
		$user = $this->entityManager->getRepository(User::class)->find($id);
		// Avec ParamConverter, $user est déjà l'objet User correspondant à l'ID dans l'URL
		
		// Vérifier si l'utilisateur existe
		if (!$user) {
			$this->addFlash('error', 'Utilisateur non trouvé.');
			return $this->redirectToRoute("adminUsers");
		}

		// Procéder à la suppression
		$this->entityManager->remove($user);
		$this->entityManager->flush();

		// Ajouter un message flash pour confirmation
		$this->addFlash('success', 'Utilisateur supprimé avec succès.');

		// Rediriger vers la liste des utilisateurs
		return $this->redirectToRoute("adminUsers");
	}



	#[Route('/admin/orders/modifier', name: 'adminCommandesModifier')]
	public function adminCommandesModifierAction(Request $request): Response
	{
		$commandeId = $request->query->get('id') ?: $request->request->get('id');
		$commande = $this->entityManager->getRepository(Commande::class)->find($commandeId);
	
		$commande = $entityManager->getRepository(Commande::class)->find($id);

		if (!$commande) {
			return $this->redirectToRoute('adminOrders');
		}

		$detailsCommande = $this->obtenirDetailsCommande($commande);

	
		

		$form = $this->createForm(CommandeType::class, $commande);
    	$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Mise à jour des champs non mappés manuellement
			$this->miseAJourCommande($form, $commande);
		
		
			return $this->redirectToRoute('adminOrders');
		}

		return $this->render('admin/admin.form.html.twig', [
			'form' => $form->createView(),
			'detailsCommande' => $detailsCommande,
		]);
	}



    
	#[Route('/admin/users/modifier', name: 'adminUtilisateursModifier')]
	public function adminUtilisateursModifierAction(Request $request): Response
	{
		$userId = $request->query->get('id') ?: $request->request->get('id');
		$user = $this->entityManager->getRepository(User::class)->find($userId);
		// Le ParamConverter injecte automatiquement l'objet User

		// Vérifier si l'utilisateur existe
		if (!$user) {
			$this->addFlash('error', 'Utilisateur non trouvé.');
			return $this->redirectToRoute('adminUsers');
		}

		// Créer et gérer le formulaire
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// Mettre à jour l'utilisateur
			$this->miseAJourUtilisateur($form, $user);

			// Ajouter un message flash de succès
			$this->addFlash('success', 'Utilisateur modifié avec succès.');

			// Rediriger vers la liste des utilisateurs
			return $this->redirectToRoute('adminUsers');
		}

		// Afficher le formulaire de modification
		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}




	#[Route('/admin/users/ajouter', name: 'adminUtilisateursAjouter')]
	public function adminUtilisateursAjouterAction(Request $request): Response {
		$user = new User();
		$form = $this->handleForm($request, $user, UserType::class, 'adminUsers');

		if ($form instanceof Response) {
			return $form;
		}

		return $this->render('admin/admin.form.html.twig', ['form' => $form->createView()]);
	}

    private function obtenirDetailsCommande(Commande $commande): array
	{
		$prixTotal = 0;
		$articlesDetails = [];

		foreach ($commande->getLigneCommandes() as $ligne) {
			$prixTotal += $ligne->getPrixTotal();
			$articlesDetails[] = $ligne->getArticle()->getTitre() . " - Quantité: " . $ligne->getQuantite() . ", Prix unitaire: " . $ligne->getPrix() . " €";
		}

		return [
			'prixTotal' => $prixTotal,
			'articlesDetails' => implode("\n", $articlesDetails)
		];
	}

	private function miseAJourCommande(FormInterface $form, Commande $commande)
    {
        // Mise à jour des champs non mappés manuellement
        $commande->setStatutCommande($form->get('statutCommande')->getData());

        if ($commande->getUser() === null && $form->has('client')) {
            $commande->setEmailVisiteur($form->get('client')->getData());
        }

        $commande->setNomCommande($form->get('nomCommande')->getData());
        $commande->setPrenomCommande($form->get('prenomCommande')->getData());
        $commande->setNumeroTel($form->get('numeroTel')->getData());
        $commande->setAdresseLivraison($form->get('adresseLivraison')->getData());
        $commande->setVilleLivraison($form->get('villeLivraison')->getData());
        $commande->setCodeLivraison($form->get('codeLivraison')->getData());
        $commande->setPaysLivraison($form->get('paysLivraison')->getData());
        $commande->setAdresseFacturation($form->get('adresseFacturation')->getData());
        $commande->setVilleFacturation($form->get('villeFacturation')->getData());
        $commande->setCodeFacturation($form->get('codeFacturation')->getData());
        $commande->setPaysFacturation($form->get('paysFacturation')->getData());

        // Persister et flusher l'entité mise à jour
        $this->entityManager->persist($commande);
        $this->entityManager->flush();
		
    }


	private function miseAJourUtilisateur($form, User $user)
	{
		// Mise à jour du rôle de l'utilisateur
		$role = $form->get('roles')->getData();
		$user->setRoles([$role]);

		// Hashage et mise à jour du mot de passe
		if ($form->get('password')->getData()) {
			$user->setPassword($this->passwordHasher->hashPassword($user, $form->get('password')->getData()));
		}

		// Mise à jour des autres informations du compte utilisateur
		$user->setEmail($form->get('email')->getData());
		$user->setNomCompte($form->get('nomCompte')->getData());
		$user->setPrenomCompte($form->get('prenomCompte')->getData());
		$user->setNumTelCompte($form->get('numTelCompte')->getData());
		$user->setAdressePostaleCompte($form->get('adressePostaleCompte')->getData());
		$user->setCodePostalCompte($form->get('codePostalCompte')->getData());
		$user->setVilleCompte($form->get('villeCompte')->getData());
		$user->setPaysCompte($form->get('paysCompte')->getData());

		// Mise à jour de l'état de vérification de l'email
		$user->setIsVerified($form->get('isVerified')->getData());

		// Persister les modifications
		$this->entityManager->persist($user);

		// Appliquer les modifications dans la base de données
		$this->entityManager->flush();
	}


	private function handleForm(Request $request, $entity, $formType, $redirectRoute) {
		$form = $this->createForm($formType, $entity);
		$form->handleRequest($request);
	
		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->persist($entity);
			$this->entityManager->flush();
	
			$this->addFlash('success', 'Opération effectuée avec succès.');
			return $this->redirectToRoute($redirectRoute);
		}
	
		return $form;
	}


	/**
	 * Méthode privée pour gérer la suppression d'entités.
	 *
	 * @param string $entityClass La classe de l'entité à supprimer.
	 * @param int $id L'identifiant de l'entité à supprimer.
	 * @param string $redirectRoute La route de redirection après la suppression.
	 * @return Response
	 */
	private function handleDelete(string $entityClass, int $id, string $redirectRoute): Response {
		$entity = $this->entityManager->getRepository($entityClass)->find($id);
		
		if ($entity) {
			$this->entityManager->remove($entity);
			$this->entityManager->flush();
			$this->addFlash('success', 'L\'entité a été supprimée avec succès.');
		} else {
			$this->addFlash('error', 'L\'entité n\'a pas été trouvée.');
		}

		return $this->redirectToRoute($redirectRoute);
	}

	/**
	 * Trouve une entité par son ID ou redirige si elle n'est pas trouvée.
	 *
	 * @param string $entityClass Le nom complet de la classe de l'entité.
	 * @param int $id L'ID de l'entité à trouver.
	 * @param string $redirectRoute La route de redirection si l'entité n'est pas trouvée.
	 * @return object|Response L'entité trouvée ou une réponse de redirection.
	 */
	private function findEntityOrRedirect(string $entityClass, int $id, string $redirectRoute) {
		$entity = $this->entityManager->getRepository($entityClass)->find($id);

		if (!$entity) {
			$this->addFlash('error', 'Entité non trouvée.');
			return $this->redirectToRoute($redirectRoute);
		}

		return $entity;
	}


	/**
	 * Envoie des notifications de disponibilité d'un article.
	 *
	 * @param $article L'article concerné par la notification.
	 * @param int $ancienneDisponibilite L'ancienne disponibilité de l'article.
	 * @param MailerInterface $mailer Le service de messagerie pour envoyer des emails.
	 * @param NotificationsStockRepository $notificationsStockRepository Le dépôt des notifications.
	 */
	private function envoyerNotificationsDisponibilite($article, int $ancienneDisponibilite, MailerInterface $mailer, NotificationsStockRepository $notificationsStockRepository) {
		if ($ancienneDisponibilite == 0 && $article->getDisponibilite() > 0) {
			$notifications = $notificationsStockRepository->findBy(['article' => $article]);

			foreach ($notifications as $notification) {
				$email = (new Email())
					->from("johanne.vgrx@gmail.com")
					->to($notification->getEmail())
					->subject("Article de retour en stock")
					->html("L'article " . $article->getTitre() . " est de nouveau disponible sur notre site.");

				$mailer->send($email);

				$this->entityManager->remove($notification);
			}
			$this->entityManager->flush();
		}
	}


	

}

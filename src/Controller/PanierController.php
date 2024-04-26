<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Catalogue\Article;
use App\Entity\Panier\Panier;
use App\Entity\Commande\CommandePdf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Commande\Commande;
use App\Entity\Commande\LigneCommande;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\PDFService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PanierController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private Panier $panier;
    private PDFService $pdfService;

    public function __construct(PDFService $pdfService, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->pdfService = $pdfService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/ajouterLigne', name: 'ajouterLigne')]
    public function ajouterLigneAction(Request $request): Response {
        $session = $request->getSession();
        $this->initialiserPanier($session);

        $quantite = $request->request->get('quantite', 1); // Récupérer la quantité de la requête
        $article = $this->entityManager->getReference(Article::class, $request->query->get("id"));
        $this->panier->ajouterLigne($article, $quantite); // Ajouter l'article avec la quantité spécifiée
        $session->set("panier", $this->panier);

        return $this->render('panier/panier.html.twig', ['panier' => $this->panier]);
    }


    #[Route('/supprimerLigne', name: 'supprimerLigne')]
    public function supprimerLigneAction(Request $request): Response
    {
        $session = $request->getSession();
        $this->initialiserPanier($session);

        $this->panier->supprimerLigne($request->query->get("id"));
        $session->set("panier", $this->panier);

        if ($this->panier->getLignesPanier()->count() === 0) {
            $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
            $articles = $query->getResult();
            return $this->render('panier/panier.vide.html.twig', [
                'articles' => $articles,
            ]);
        }
        

        return $this->render('panier/panier.html.twig', ['panier' => $this->panier]);
    }

    

    #[Route('/viderPanier', name: 'viderPanier', methods: ["POST"])]
    public function viderPanierAction(Request $request): Response
    {
        $session = $request->getSession();
        $this->initialiserPanier($session);

        // Reset du panier
        $this->panier = new Panier();
        $session->set("panier", $this->panier);

        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
            $articles = $query->getResult();
            return $this->render('panier/panier.vide.html.twig', [
                'articles' => $articles,
            ]);
    }





    #[Route('/recalculerPanier', name: 'recalculerPanier', methods: ["GET", "POST"])]
    public function recalculerPanierAction(Request $request): Response
    {
        $this->logger->info('La fonction recalculerPanierAction a été appelée.');
        $session = $request->getSession();   
        $this->initialiserPanier($session);

        if ($request->isXmlHttpRequest()) {
            return $this->handleAjaxRequest($request);
        } else {
            return $this->handleFormSubmission($request, $session);
        }
    }


    #[Route('/miseAJourPanierAjax', name: 'miseAJourPanierAjax', methods: ["POST"])]
    public function miseAJourPanierAjaxAction(Request $request, SessionInterface $session): JsonResponse 
    {
        $session = $request->getSession();
        $this->initialiserPanier($session);

        

        $data = json_decode($request->getContent(), true);
        $articleId = $data['id'];
        $quantite = $data['quantite'];

        // Trouver l'article dans le panier et mettre à jour la quantité
        foreach ($this->panier->getLignesPanier() as $ligne) {
            if ($ligne->getArticle()->getId() == $articleId) {
                $ligne->setQuantite($quantite);
                $ligne->recalculer();
                break; // Quitter la boucle une fois l'article trouvé et mis à jour
            }
        }

        $this->panier->recalculer();

        $this->panier->recalculer();
        $session->set("panier", $this->panier);
        

        return new JsonResponse([
            'message' => 'Panier mis à jour',
            'nouveauTotal' => $this->panier->getTotal()
        ]);
    }

	 
    #[Route('/accederAuPanier', name: 'accederAuPanier')]
    public function accederAuPanierAction(SessionInterface $session): Response
    {
        
        // Récupérer le panier de la session ou créer un nouveau panier si non existant
        $panier = $session->get('panier', new Panier());

        $produitAvecTotalZero = false;

        foreach ($panier->getLignesPanier() as $lignePanier) {
            if ($lignePanier->getPrixTotal() <= 0) {
                $produitAvecTotalZero = true;
                break;
            }
        }

        // Utiliser la variable locale $panier au lieu de la propriété de classe $this->panier
        if ($panier->getLignesPanier()->count() === 0) {
            $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
            $articles = $query->getResult();
            return $this->render('panier/panier.vide.html.twig', [
                'articles' => $articles,
            ]);
        }
        else {
            return $this->render('panier/panier.html.twig', [
                'panier' => $panier,
                'produitAvecTotalZero' => $produitAvecTotalZero
            ]);
        }
    }


    #[Route('/accederCommande', name: 'accederCommande')]
    public function accederCommande(SessionInterface $session, AuthenticationUtils $authenticationUtils): Response {
        // Vérifie si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            // Si l'utilisateur est connecté, rediriger vers 'commanderPanier'
            return $this->redirectToRoute('commanderPanier');
        }

        // Si l'utilisateur n'est pas connecté, afficher le formulaire de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $panier = $session->get('panier', new Panier());


        return $this->render('security/acceder_commande.html.twig', [
            'panier' => $panier,
            'error' => $error,
            'last_username' => $lastUsername
        ]);

    }



    #[Route('/commanderPanier', name: 'commanderPanier', methods: ['GET', 'POST'])]
    public function commanderPanierAction(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response {
        $panier = $session->get('panier', new Panier());

        if (empty($panier->getLignesPanier())) {
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('accederAuPanier');
        }

        if ($request->isMethod('POST')) {
            $commande = $this->creerCommande($request, $entityManager, $panier);
            
            // Ajouter les lignes de commande et mettre à jour les stocks
            foreach ($panier->getLignesPanier() as $lignePanier) {
                $article = $entityManager->getRepository(Article::class)->find($lignePanier->getArticle()->getId());
                $quantiteCommandee = $lignePanier->getQuantite();
                $nouveauStock = $article->getDisponibilite() - $quantiteCommandee;

                if ($nouveauStock >= 0) {
                    $article->setDisponibilite($nouveauStock);
                    $entityManager->persist($article);
                } else {
                    $this->addFlash('error', "Pas assez de stock pour l'article {$article->getTitre()}");
                    return $this->redirectToRoute('accederAuPanier');
                }
            }

            $entityManager->persist($commande);
            $entityManager->flush();

            $session->set('panier', new Panier());
            return $this->redirectToRoute('confirmation_page', ['id' => $commande->getId()]);
        }

        $userInfo = $this->getUserInfo();
        return $this->render('commande/commande.html.twig', [
            'panier' => $panier,
            'user' => $userInfo,
        ]);
    }






    #[Route('/confirmation/{id}', name: 'confirmation_page')]
    public function orderConfirmation($id, EntityManagerInterface $entityManager, MailerInterface $mailer): Response {

        $commande = $entityManager->getRepository(Commande::class)->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $user = $commande->getUser();
        // Utilisation de PDFService pour générer le contenu du PDF
        $pdfContent = $this->pdfService->createCommandePdf($commande);

        // Créer une instance de CommandePdf et stocker la chaîne PDF
        $commandePdf = new CommandePdf();
        $commandePdf->setPdfContent($pdfContent);
        $commandePdf->setCommande($commande);
        $commandePdf->setCreatedAt(new \DateTime());
        $this->entityManager->persist($commandePdf);
        
        $this->entityManager->flush();

        // Envoi de l'email avec pièce jointe PDF
        $email = (new Email())
            ->from('johanne.vgrx@gmail.com')
            ->to($commande->getEmailVisiteur())
            ->subject('Confirmation de commande')
            ->html($this->renderView('emails/confirmation.html.twig', ['name' => $commande->getNomCommande(), 'commande' => $commande]))
            ->attach($pdfContent, 'commande_' . $id . '.pdf', 'application/pdf');

        $mailer->send($email);

        return $this->render('commande/confirmation.html.twig', ['commande' => $commande]);
    }



    #[Route('/download-commande-pdf/{id}', name: 'download_commande_pdf')]
    public function downloadCommandePdf($id, EntityManagerInterface $entityManager): Response {
        // Trouver la commande associée à l'ID
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        // Vérifier si la commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Commande not found.');
        }

        
        $pdfContent = $this->pdfService->createCommandePdf($commande);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment;filename="commande_' . $id . '.pdf"');

        return $response;
    }




    private function initialiserPanier(SessionInterface $session): void
    {
        $this->demarrerSession($session);
        if ($session->has("panier"))
            $this->panier = $session->get("panier");
        else
            $this->panier = new Panier();
    }

    private function demarrerSession(SessionInterface $session): void
    {
        if (!$session->isStarted()) {
            $session->start();
        }
    }

    private function ajouterLignesCommande(Commande $commande, Panier $panier, EntityManagerInterface $entityManager): void {
        foreach ($panier->getLignesPanier() as $lignePanier) {
            $article = $entityManager->merge($lignePanier->getArticle());
            $ligneCommande = new LigneCommande();
            $ligneCommande->setArticle($article);
            $ligneCommande->setPrix($article->getPrix());
            $ligneCommande->setQuantite($lignePanier->getQuantite());
            $ligneCommande->setPrixTotal($lignePanier->getQuantite() * $article->getPrix());
    
            $entityManager->persist($ligneCommande);
            $commande->addLigneCommande($ligneCommande);
        }
    }

    private function creerCommande(Request $request, EntityManagerInterface $entityManager, Panier $panier): Commande {
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatutCommande('En attente');
    
        $user = $this->getUser();
        if ($user) {
            // Utilisation des informations de l'utilisateur connecté
            $commande->setUser($user);
            $commande->setEmailVisiteur($user->getEmail());
            $commande->setNomCommande($user->getNomCompte());
            $commande->setPrenomCommande($user->getPrenomCompte());
            $commande->setNumeroTel($user->getNumTelCompte());
            $commande->setAdresseLivraison($user->getAdressePostaleCompte());
            $commande->setCodeLivraison($user->getCodePostalCompte());
            $commande->setVilleLivraison($user->getVilleCompte());
            $commande->setPaysLivraison($user->getPaysCompte());
        } else {
            // Utilisation des données fournies dans le formulaire pour les visiteurs
            $commande->setEmailVisiteur($request->request->get('email'));
            $commande->setNomCommande($request->request->get('last_name'));
            $commande->setPrenomCommande($request->request->get('first_name'));
            $commande->setNumeroTel($request->request->get('telephone'));
            $commande->setAdresseLivraison($request->request->get('address_livraison'));
            $commande->setCodeLivraison($request->request->get('postal_code_livraison'));
            $commande->setVilleLivraison($request->request->get('city_livraison'));
            $commande->setPaysLivraison($request->request->get('country_livraison'));
            $commande->setAdresseFacturation($request->request->get('address_facturation'));
            $commande->setCodeFacturation($request->request->get('postal_code_facturation'));
            $commande->setVilleFacturation($request->request->get('city_facturation'));
            $commande->setPaysFacturation($request->request->get('country_facturation'));
        }
    
        return $commande;
    }


    private function getUserInfo(): array {
        $user = $this->getUser();
        if (!$user) {
            return [];
        }
    
        return [
            'first_name' => $user->getPrenomCompte(), // Prénom de l'utilisateur
            'last_name' => $user->getNomCompte(), // Nom de famille de l'utilisateur
            'email' => $user->getEmail(), // Email de l'utilisateur
            'telephone' => $user->getNumTelCompte(), // Numéro de téléphone de l'utilisateur
            'address' => $user->getAdressePostaleCompte(), // Adresse postale de l'utilisateur
            'city' => $user->getVilleCompte(), // Ville de l'utilisateur
            'postal_code' => $user->getCodePostalCompte(), // Code postal de l'utilisateur
            'country' => $user->getPaysCompte() // Pays de l'utilisateur
        ];
    }
    

    private function handleAjaxRequest(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $stockInsuffisant = $this->updatePanierQuantities($data);
    
        if ($stockInsuffisant) {
            return new JsonResponse(['error' => 'Quantité demandée supérieure au stock disponible.']);
        }
    
        return new JsonResponse(['message' => 'Panier mis à jour', 'nouveauTotal' => $this->panier->getTotal()]);
    }
    
    private function handleFormSubmission(Request $request, SessionInterface $session): Response {
        $cartData = $request->request->all('cart');
        $stockInsuffisant = $this->updatePanierQuantities($cartData);
    
        if ($stockInsuffisant) {
            $session->getFlashBag()->add('error', 'Quantité demandée supérieure au stock disponible pour certains articles.');
            return $this->redirectToRoute('accederAuPanier');
        }
    
        return $this->render('panier/panier.html.twig', ['panier' => $this->panier]);
    }
    
    private function updatePanierQuantities(array $data): bool {
        $stockInsuffisant = false;
    
        foreach ($data as $articleId => $quantite) {
            $ligne = $this->panier->chercherLignePanierParId($articleId);
            if (!$ligne) {
                continue;
            }
    
            if ($quantite > $ligne->getArticle()->getDisponibilite()) {
                $stockInsuffisant = true;
                break;
            }
    
            $ligne->setQuantite($quantite);
            $ligne->recalculer();
        }
    
        if (!$stockInsuffisant) {
            $this->panier->recalculer();
        }
    
        return $stockInsuffisant;
    }
    
    
    


    

    
}

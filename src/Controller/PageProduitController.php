<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Catalogue\Article;
use App\Entity\Avis;
use App\Entity\NotificationsStock;
use Symfony\Component\Security\Core\Security;

class PageProduitController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private Security $security;
    
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Security $security)  {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->security = $security;
    }

    #[Route('/page_produit', name: 'pageProduit')]
public function indexAction(Request $request): Response
{
    $idProduit = $request->query->get('id');

    if (!$idProduit) {
        throw $this->createNotFoundException('Aucun ID de produit spécifié.');
    }

    // Modifiez la requête pour inclure les avis relatifs à l'article
    $query = $this->entityManager->createQuery(
        "SELECT a, av FROM App\Entity\Catalogue\Article a 
        LEFT JOIN a.avis av
        WHERE a.id = :idProduit"
    )->setParameter('idProduit', $idProduit);

    $article = $query->getOneOrNullResult();

    if (!$article) {
        throw $this->createNotFoundException('Article not found.');
    }

    // Passez l'article et ses avis à la vue
    return $this->render('produit/page_produit.html.twig', [
        'article' => $article,
        'avis' => $article->getAvis() // Assurez-vous que votre entité Article a une méthode getAvis()
    ]);
}



    #[Route("/notifier-disponibilite", name: "notifierDisponibilite", methods: ["POST"])]
    public function notifierDisponibilite(Request $request, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');
        $articleId = $request->request->get('article_id');

        $article = $entityManager->getRepository(Article::class)->find($articleId);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        // Vérifiez si une notification pour cet e-mail et cet article existe déjà
        $existingNotification = $entityManager->getRepository(NotificationsStock::class)->findOneBy([
            'email' => $email,
            'article' => $article
        ]);

        if (!$existingNotification) {
            // Créez la notification seulement si elle n'existe pas
            $notification = new NotificationsStock();
            $notification->setEmail($email);
            $notification->setArticle($article);

            $entityManager->persist($notification);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été enregistrée. Nous vous contacterons lorsque l\'article sera de nouveau disponible.');
        } else {
            // Si une notification existe déjà, indiquez à l'utilisateur qu'il a déjà été enregistré
            $this->addFlash('info', 'Vous avez déjà demandé à être notifié pour cet article.');
        }

        return $this->redirectToRoute('pageProduit', ['id' => $articleId]);
    }

    #[Route('/ajouter_avis/{id}', name: 'ajouter_avis', methods: ['POST'])]
    public function ajouterAvis(Request $request, int $id): Response
    {
        $article = $this->entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            $this->addFlash('error', 'Article non trouvé');
            return $this->redirectToRoute('homepage'); // Redirigez vers une route appropriée si l'article n'est pas trouvé
        }
        

        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');

        if ($this->security->getUser()) { // Vérifiez si l'utilisateur est connecté
            $avis = new Avis();
            $avis->setArticle($article);
            $avis->setUser($this->security->getUser()); 
            $avis->setNoteAvis($note);
            $avis->setCommentaireAvis($commentaire);
            $avis->setDateAvis(new \DateTime());

            $this->entityManager->persist($avis);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');
        } else {
            $this->addFlash('error', 'Vous devez être connecté pour poster un avis.');
        }

        return $this->redirectToRoute('pageProduit', ['id' => $id]); // Assurez-vous que la route 'pageProduit' existe
    }

    
}

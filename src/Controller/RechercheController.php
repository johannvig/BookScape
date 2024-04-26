<?php

// Ensure that PHP opening tag is at the top of the file and namespaces are declared at the beginning.

namespace App\Controller;

// Use statements are grouped together.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

// Class names must be declared in StudlyCaps.
class RechercheController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    // Constructor parameters are aligned properly.
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    // Methods are named in camelCase.
    // Annotations should directly precede the function without a blank line.
    #[Route('/afficheRecherche', name: 'afficheRecherche')]
    public function afficheRecherche(Request $request): Response
    {
        // Proper indentation and alignment.
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
        $articles = $query->getResult();

        // Array structure is properly formatted.
        return $this->render('produit/recherche.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/afficheRechercheParMotCle', name: 'afficheRechercheParMotCle')]
    public function afficheRechercheParMotCle(Request $request): Response
    {
        $motCle = $request->query->get("titre");
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a WHERE a.titre LIKE :motCle");
        $query->setParameter("motCle", '%'.addslashes($motCle).'%');
        $articles = $query->getResult();

        return $this->render('produit/recherche.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/afficheRechercheFiltree', name: 'afficheRechercheFiltree')]
    public function afficheRechercheFiltree(Request $request): Response
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')
           ->from('App\Entity\Catalogue\Article', 'a');

        // Use strict comparison (===) when possible for type checking.
        // Query parameters are handled securely without directly injecting into the query.
        if ($type = $request->query->get('type')) {
            $qb->andWhere('a INSTANCE OF App\Entity\Catalogue\\' . ucfirst($type));
        }

        if ($titre = $request->query->get('titre')) {
            $qb->andWhere('a.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
        

        if ($prixMin = $request->query->get('prix_min')) {
            $qb->andWhere('a.prix >= :prix_min')
               ->setParameter('prix_min', $prixMin);
        }

        if ($prixMax = $request->query->get('prix_max')) {
            $qb->andWhere('a.prix <= :prix_max')
               ->setParameter('prix_max', $prixMax);
        }

        if ($auteur = $request->query->get('auteur')) {
            $qb->andWhere('a.auteur LIKE :auteur')
               ->setParameter('auteur', '%' . $auteur . '%');
        }

        if ($artiste = $request->query->get('artiste')) {
            $qb->andWhere('a.artiste LIKE :artiste')
               ->setParameter('artiste', '%' . $artiste . '%');
        }
        
        $articles = $qb->getQuery()->getResult();

        return $this->render('produit/recherche.html.twig', [
            'articles' => $articles,
        ]);
    }


    


}

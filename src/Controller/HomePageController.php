<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class HomePageController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/homepage', name: 'homepage')]
    public function indexAction(Request $request): Response
    {
        $query = $this->entityManager->createQuery("SELECT a FROM App\Entity\Catalogue\Article a");
        $articles = $query->getResult();
        return $this->render('homepage.html.twig', [
            'articles' => $articles,
        ]);
    }



}

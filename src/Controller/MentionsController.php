<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MentionsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)  {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/mentions', name: 'mentions')]
    public function indexAction(Request $request): Response
    {
        return $this->render('mentions.html.twig', [
        ]);
    }



}

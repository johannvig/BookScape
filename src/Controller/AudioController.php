<?php
namespace App\Controller;

use App\Entity\Audio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AudioController extends AbstractController
{
    #[Route('/audios', name: 'audios')]
    public function currentAudio(EntityManagerInterface $entityManager): Response
    {
        $audio = $entityManager->getRepository(Audio::class)->findOneBy([]);
    
        if (!$audio) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }
    
        $article = $audio->getArticle();
        $enEcoute = $audio->isStatutEcoute();
    
        return $this->render('audio.html.twig', [
            'audio' => $audio,
            'article' => $article,
            'enEcoute' => $enEcoute,
        ]);
    }
    

}


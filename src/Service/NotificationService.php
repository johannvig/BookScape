<?php
// src/Service/NotificationService.php
namespace App\Service;

use App\Entity\Catalogue\Article;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\NotificationsStockRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService {
    private $mailer;
    private $notificationsRepo;
    private $entityManager;

    public function __construct(
        MailerInterface $mailer, 
        NotificationsStockRepository $notificationsRepo,
        EntityManagerInterface $entityManager
    ) {
        $this->mailer = $mailer;
        $this->notificationsRepo = $notificationsRepo;
        $this->entityManager = $entityManager;
    }

    public function sendNotifications(Article $article, int $oldAvailability) {
        if ($oldAvailability == 0 && $article->getDisponibilite() > 0) {
            $notifications = $this->notificationsRepo->findBy(['article' => $article]);

            foreach ($notifications as $notification) {
                $email = (new Email())
                    ->from("johanne.vgrx@gmail.com")
                    ->to($notification->getEmail())
                    ->subject("Article de retour en stock")
                    ->html("L'article " . $article->getTitre() . " est de nouveau disponible sur notre site.");

                $this->mailer->send($email);

                $this->entityManager->remove($notification);
            }
            $this->entityManager->flush();
        }
    }
}


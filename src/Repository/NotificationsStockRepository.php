<?php

namespace App\Repository;

use App\Entity\NotificationsStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<NotificationsStock>
 *
* @method NotificationsStock|null find($id, $lockMode = null, $lockVersion = null)
* @method NotificationsStock|null findOneBy(array $criteria, array $orderBy = null)
* @method NotificationsStock[]    findAll()
* @method NotificationsStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class NotificationsStockRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry)
   {
       parent::__construct($registry, NotificationsStock::class);
   }

   /**
    * Récupère toutes les notifications pour un article donné.
    *
    * @param int $articleId L'ID de l'article.
    * @return NotificationsStock[] Retourne un tableau d'objets NotificationsStock.
    */
   public function findByArticle($articleId): array
   {
       return $this->createQueryBuilder('n')
           ->andWhere('n.article = :articleId')
           ->setParameter('articleId', $articleId)
           ->getQuery()
           ->getResult();
   }

   public function findOneByEmailAddressAndArticle($email, $article)
   {
       return $this->createQueryBuilder('n')
           ->andWhere('n.email = :email')
           ->andWhere('n.article = :article')
           ->setParameter('email', $email)
           ->setParameter('article', $article)
           ->getQuery()
           ->getOneOrNullResult();
   }
}
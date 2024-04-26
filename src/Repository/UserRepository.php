<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Recherche un utilisateur par son adresse email.
     *
     * @param string $email L'adresse email à rechercher.
     * @return User|null L'utilisateur trouvé, ou null si aucun utilisateur n'est trouvé.
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.email) = :email') // Recherche insensible à la casse
            ->setParameter('email', strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find a user by reset token.
     *
     * @param string $resetToken The reset token to search for.
     * @return User|null The user entity or null if not found.
     */
    public function findOneByResetToken(string $resetToken): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.resetToken = :resetToken')
            ->setParameter('resetToken', $resetToken)
            ->getQuery()
            ->getOneOrNullResult();
    }

   

}

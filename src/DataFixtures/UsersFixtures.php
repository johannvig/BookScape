<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $adminUser = $this->createUser(
            'admin@example.com',
            'Admin',
            'Administrator',
            '0123456789',
            '123 Admin Street',
            12345,
            'Adminville',
            'France',
            true,
            true,
            ['ROLE_ADMIN'],
            'test'
        );
        $manager->persist($adminUser);

        // Create regular user
        $user = $this->createUser(
            'user@example.com',
            'user',
            'User',
            '0123456789',
            '123 User Street',
            12345,
            'Laval',
            'France',
            false,
            true,
            ['ROLE_USER'],
            'test'
        );
        $manager->persist($user);

        // Create regular user
        $user2 = $this->createUser(
            'user2@example.com',
            'user2',
            'User2',
            '0123456781',
            '123 User Street',
            12345,
            'Laval',
            'France',
            false,
            true,
            ['ROLE_USER'],
            'test'
        );

        $manager->persist($user2);

        $user3 = $this->createUser(
            'johanne.vgrx@gmail.com',
            'Admin',
            'Administrator',
            '0123456789',
            '123 Admin Street',
            12345,
            'Adminville',
            'France',
            true,
            true,
            ['ROLE_USER'],
            'test'
        );
        $manager->persist($user3);

        $manager->flush();
    }

    private function createUser(
        string $email,
        string $nom,
        string $prenom,
        string $tel,
        string $adresse,
        int $codePostal,
        string $ville,
        string $pays,
        bool $isAdmin,
        bool $verify,
        array $roles,
        string $password
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setNomCompte($nom);
        $user->setPrenomCompte($prenom);
        $user->setNumTelCompte($tel);
        $user->setAdressePostaleCompte($adresse);
        $user->setCodePostalCompte($codePostal);
        $user->setVilleCompte($ville);
        $user->setPaysCompte($pays);
        $user->setIsAdmin($isAdmin);
        $user->setRoles($roles);
        $user->setIsVerified($verify);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }
}

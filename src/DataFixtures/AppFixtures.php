<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Créer l'ADMIN (Il aura l'ID 1 si c'est le premier persist)
        $admin = new User();
        $admin->setUsername('SuperAdmin');
        $admin->setAvatar('https://i.pravatar.cc/150?u=admin');
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // 2. Créer l'USER (Il aura l'ID 2)
        $user = new User();
        $user->setUsername('JeanDupont');
        $user->setAvatar('https://i.pravatar.cc/150?u=jean');
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // 3. Créer un article pour l'ADMIN
        $artAdmin = new Article();
        $artAdmin->setTitre("Article de l'Admin");
        $artAdmin->setContenu("Ceci est le contenu obligatoire pour l'article de l'admin."); // <-- AJOUTE CETTE LIGNE
        $artAdmin->setAuthor($admin); // <--- Ici on lie à l'ID 1
        $manager->persist($artAdmin);

        // 4. Créer un article pour l'USER
        $artUser = new Article();
        $artUser->setTitre("Article de l'User");
        $artUser->setAuthor($user); // <--- Ici on lie à l'ID 2
        $artUser->setContenu("Ceci est le contenu obligatoire pour l'article de l'utilisateur."); // <-- AJOUTE CETTE LIGNE
        $manager->persist($artUser);
        
        $manager->flush();
    }
}

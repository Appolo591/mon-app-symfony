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
        // 1. Créer un utilisateur de test
        $user = new User();
        $user->setEmail('admin@test.com');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        // 2. Créer quelques articles
        for ($i = 1; $i <= 5; $i++) {
            $article = new Article();
            $article->setTitre("Article de test n°$i");
            $article->setContenu("Contenu de l'article n°$i");
            $manager->persist($article);
        }

        $manager->flush();
    }
}

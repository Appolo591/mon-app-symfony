<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setTitre("Mon article n°" . $i);
            $article->setContenu("Ceci est le contenu incroyable de l'article numéro " . $i);
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}

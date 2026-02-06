<?php

namespace App\Controller;

use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // On récupère TOUS les articles de la base de données
        $articles = $articleRepository->findAll();

        // On les envoie à la vue Twig
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/nouveau', name: 'app_article_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('home/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/supprimer/{id}', name: 'app_article_delete', methods: ['POST', 'GET'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        // On demande à l'EntityManager de supprimer l'objet
        $em->remove($article);
        $em->flush();

        // On ajoute un petit message de succès (facultatif mais pro)
        $this->addFlash('success', 'Article supprimé.');

        return $this->redirectToRoute('app_home');
    }
}

// final class HomeController extends AbstractController
// {
//     #[Route('/', name: 'app_home')]
//     public function index(): Response
//     {
//         return $this->render('home/index.html.twig', [
//             'controller_name' => 'HomeController',
//         ]);
//     }
// }

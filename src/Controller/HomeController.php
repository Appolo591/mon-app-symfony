<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
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
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);


        // On les envoie à la vue Twig
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/nouveau', name: 'app_article_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        // 1. Création de l'objet Comment et du formulaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        // 2. Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // On lie le commentaire à l'utilisateur connecté et à l'article
            $comment->setAuthor($this->getUser());
            $comment->setArticle($article);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été publié !');

            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('home/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // C'est ici que la magie opère :
        // Symfony appelle ton ArticleVoter. 
        // Si l'utilisateur n'est pas Admin ET n'est pas l'auteur, une erreur 403 est lancée.
        $this->denyAccessUnlessGranted('EDIT', $article);

        // On crée le formulaire en le remplissant avec les données de l'objet $article
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de $entityManager->persist($article) ici car l'article existe déjà
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/supprimer/{id}', name: 'app_article_delete', methods: ['POST', 'GET'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        // Cette ligne bloque l'accès direct via l'URL si le Voter dit "non"
        $this->denyAccessUnlessGranted('EDIT', $article);
        // On demande à l'EntityManager de supprimer l'objet
        $em->remove($article);
        $em->flush();

        // On ajoute un petit message de succès (facultatif mais pro)
        $this->addFlash('success', 'Article supprimé.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/comment/delete/{id}', name: 'app_comment_delete', methods: ['POST', 'GET'])]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {

    if ($this->getUser() !== $comment->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
    // Si l'utilisateur n'est NI l'auteur, NI admin, on bloque
    throw $this->createAccessDeniedException('Vous n\'avez pas le droit de faire ça.');
    }
    
    // Sécurité : On vérifie que l'utilisateur connecté est bien l'auteur du commentaire
    if ($this->getUser() !== $comment->getAuthor()) {
        $this->addFlash('danger', 'Vous ne pouvez pas supprimer ce commentaire.');
        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
    }

    $articleId = $comment->getArticle()->getId();
    
    $entityManager->remove($comment);
    $entityManager->flush();

    $this->addFlash('success', 'Commentaire supprimé avec succès.');

    return $this->redirectToRoute('app_article_show', ['id' => $articleId]);
}
}



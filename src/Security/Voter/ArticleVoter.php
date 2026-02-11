<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ArticleVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // 1. L'ADMIN voit tout
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        /** @var Article $subject */
        // 2. On récupère l'auteur de l'article
        $author = $subject->getAuthor();

        if (!$author) {
            return false; // Pas d'auteur = personne ne modifie (sauf admin)
        }

        // 3. LA COMPARAISON (Vérifie bien que c'est === et non !==)
        // "Est-ce que l'ID de l'auteur de l'article est ÉGAL à mon ID ?"
        return $author->getId() === $user->getId();
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
          yield  TextField::new('titre');
          yield  TextEditorField::new('contenu');
          yield  DateTimeField::new('createdAt')->onlyOnIndex(); // La date seulement dans la liste
          yield  AssociationField::new('author', 'Auteur');
          yield  AssociationField::new('category', 'La Catégorie')
            ->setRequired(true)
            ->autocomplete();
        
    }
    
}

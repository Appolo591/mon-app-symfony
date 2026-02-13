<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu')
            ->add('category', EntityType::class, [
                'class' => Category::class, // L'entité à utiliser
                'choice_label' => 'titre',  // La propriété à afficher dans la liste
                'label' => 'Sélectionnez une catégorie',
                'placeholder' => '--- Choisissez une option ---',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

<?php
// src/Form/BookSearchType.php

namespace App\Form;

use App\DTO\BookSearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', SearchType::class, [
                'label' => 'Titre',
                'required' => false,
            ])
            ->add('author', SearchType::class, [
                'label' => 'Auteur',
                'required' => false,
            ])
            ->add('editor', SearchType::class, [
                'label' => 'Editeur',
                'required' => false,
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'placeholder' => 'Sélectionnez un genre',
                'choices' => [
                    'Roman' => 'Roman',
                    'Fantasy' => 'Fantasy',
                    'Conte et Fable' => 'Conte et Fable',
                    'Théâtre' => 'Théâtre',
                    'Poésie' => 'Poésie',
                    'Autobiographie' => 'Autobiographie',
                ],
            ])
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Trier par',
                'required' => false,
                'placeholder' => 'Sélectionnez un critère de tri',
                'choices' => [
                    'Titre' => 'title',
                    'Auteur' => 'author',
                    'Editeur' => 'editor',
                    'Genre' => 'genre',
                    'Date de publication' => 'publication',
                ],
            ])
            ->add('direction', ChoiceType::class, [
                'label' => 'Ordre de tri',
                'required' => false,
                'placeholder' => 'Sélectionnez un ordre de tri',
                'choices' => [
                    'Croissant' => 'asc',
                    'Décroissant' => 'desc',
                ],
            ])
            ->add('limit', IntegerType::class, [
                'label' => 'Nombre de résultats',
                'required' => false,
                'empty_data' => 10,
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookSearchCriteria::class,
            'csrf_protection' => false,
        ]);
    }
}

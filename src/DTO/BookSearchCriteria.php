<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Cette classe contient les champs du formulaire de recherche
 * pour les livres.
 */
class BookSearchCriteria
{
    /**
     * Cette constantes contient tout les champs
     * pouvant trier un livre
     */
    const ORDER_FIELDS = [
        'title',
        'author',
        'editor',
        'genre',
    ];

    /**
     * Cette constante contient les possibles
     * direction de trie (ascendant ou descendant)
     */
    const DIRECTIONS = [
        'DESC',
        'ASC',
    ];

    /**
     * Contient le numéro de la page
     */
    public int $page = 1;

    /**
     * Contient la limite de livres
     * affichés à l'écran
     */
    public int $limit = 10;

    /**
     * Contient le champ de tri des livres
     */
    public string $orderBy = self::ORDER_FIELDS[0];

    /**
     * Contient la direction du tri
     */
    public string $direction = self::DIRECTIONS[0];

    /**
     * Contient le titre souhaité
     */
    public ?string $title = null;

    /**
     * Contient l'auteur souhaité
     */
    public ?string $author = null;

    /**
     * Contient l'éditeur souhaité
     */
    public ?string $editor = null;

    /**
     * Contient le genre souhaité
     */
    public ?string $genre = null;

    /**
     * Retourne les choix possibles pour le tri des livres
     */
    public static function getOrderByChoices(): array
    {
        $labels = [
            'Titre',
            'Auteur',
            'Éditeur',
            'Genre',
        ];

        return array_combine($labels, self::ORDER_FIELDS);
    }

    /**
     * Retourne les choix disponibles pour les directions 
     */
    public static function getDirectionChoices(): array
    {
        $labels = [
            'Décroissant',
            'Croissant',
        ];

        return array_combine($labels, self::DIRECTIONS);
    }
}

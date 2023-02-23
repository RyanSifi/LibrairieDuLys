<?php

namespace App\Controller;

use App\Form\SearchBookRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlleur qui s'occupe et gére la page d'accueil ainsi que la page
 * de recherche :)
 */
class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil
     */
    #[Route('/home', name: 'app_home')]
    public function home(BookRepository $repository): Response
    {
        // récupération des 10 derniers biens immobilier
       $books = $bookRepository->findLatestBooks(10);
(
            [],
            ['createdAt' => 'DESC'],
            10,
        );

        // On affiche la page d'accueil
        return $this->render('home/index.html.twig', [
            'properties' => $properties,
        ]);
    }
}
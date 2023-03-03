<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;


/**
 * Controlleur qui s'occupe et gére la page d'accueil ainsi que la page
 * de recherche :)
 */
class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    /**
     * Affiche la page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function home(Request $request, BookRepository $repository): Response
    {
        $query = $request->query->get('query');

        if ($query) {
            $books = $repository->findByTitleOrAuthor($query);
        } else {
            $books = $repository->findLatestBooks(10);
        }

        return $this->render('home/index.html.twig', [
            'books' => $books,
            'query' => $query,
        ]);
    }
}

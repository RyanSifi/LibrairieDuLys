<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;


/**
 * Controlleur qui s'occupe et gére la page d'accueil ainsi que la 
 * recherche
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
    public function genreRomans(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Roman']);

        return $this->render('book/roman.html.twig', [
            'genre' => 'Romans',
            'books' => $books
        ]);
    }

    public function genreFantasy(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Fantasy']);

        return $this->render('book/fantasy.html.twig', [
            'genre' => 'Fantasy',
            'books' => $books
        ]);
    }

    public function genreContes(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Conte et Fable']);

        return $this->render('book/conte.html.twig', [
            'genre' => 'Contes',
            'books' => $books
        ]);
    }

    public function genreTheatre(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Théâtre']);

        return $this->render('book/theatre.html.twig', [
            'genre' => 'Théâtre',
            'books' => $books
        ]);
    }

    public function genrePoesie(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Poésie']);

        return $this->render('book/poesie.html.twig', [
            'genre' => 'Poésie',
            'books' => $books
        ]);
    }

    public function genreAutobiographie(BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['genre' => 'Autobiographie']);

        return $this->render('book/autobiographie.html.twig', [
            'genre' => 'Autobiographie',
            'books' => $books
        ]);
    }
}

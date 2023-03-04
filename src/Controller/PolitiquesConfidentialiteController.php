<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PolitiquesConfidentialiteController extends AbstractController
{
    #[Route(path: '/politiques-confidentialite', name: 'app_politiques_confidentialite')]
    public function index()
    {
        return $this->render('politiques_confidentialite/index.html.twig');
    }
}

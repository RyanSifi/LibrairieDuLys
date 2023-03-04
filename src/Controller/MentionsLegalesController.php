<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MentionsLegalesController extends AbstractController
{
    #[Route(path: '/mentions-legales', name: 'app_mentions_legales')]
    public function index()
    {
        return $this->render('mentions_legales/index.html.twig');
    }
}

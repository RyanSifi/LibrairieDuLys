<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\ClientType;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controlleur frontend pour les clients. C'est ici qu'on retrouve
 * la connexion, inscription et le profil d'un client.
 */
class FrontClientController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}

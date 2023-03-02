<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CommandeController extends AbstractController
{
    #[Route('/commande/create', name: 'commande_create', methods: ['POST'])]
    public function commandeCreate(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }

        $bookId = $request->request->get('book_id');

        $commande = new Commande();
        $commande->setBookId($bookId);
        $commande->setClientId($this->getUser()->getId());
        $commande->setStatus('en attente');

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('commande_success');
    }

    #[Route('/commande/success', name: 'commande_success')]
    public function commandeSuccess(): Response
    {
        return $this->render('commande_success.html.twig');
    }
}

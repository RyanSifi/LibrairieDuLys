<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{

    #[Route("/admin", name: "admin_index")]
    public function index(Request $request, CommandeRepository $commandeRepository): Response
    {
        // Vérifie si l'utilisateur a le rôle ADMIN, si non retourne une erreur
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commandes = $commandeRepository->findAll();

        // Affiche la page de l'admin en passant l'objet $admin à la vue
        return $this->render('admin/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    #[Route("/admin/commande/delete/{id}", name: "delete_commande_admin")]
    public function admindelete(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\RegistrationFormType;
use App\Security\ClientAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, ClientAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Client();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/profil', name: 'app_profile')]
    #[Security('is_granted("ROLE_USER")')]
    public function profile(Security $security, CommandeRepository $commandeRepository): Response
    {
        $users = $security->getUser();
        $commandes = $commandeRepository->findBy(['client_id' => $users->getId()]);
        $user = $this->getUser();
        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'commandes' => $commandes,
        ]);
    }
    #[Route('/update-user-info', name: 'update_user_info')]
    public function updateUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = $this->getUser();

        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $adresse = $request->request->get('adresse');
        $sexe = $request->request->get('sexe');
        $password = $request->request->get('password');

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setAdresse($adresse);
        $user->setSexe($sexe);
        $user->setPassword(
            $userPasswordHasher->hashPassword($user, $password)
        );
        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }
}

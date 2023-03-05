<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
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
use PHPMailer\PHPMailer\PHPMailer;
use App\Repository\ClientRepository;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;


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
    #[Route('/delete-commande/{id}', name: 'delete_commande')]
    public function deleteCommande(EntityManagerInterface $entityManager, Commande $commande): Response
    {
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, PHPMailer $mailer, EntityManagerInterface $entityManager, ClientRepository $ClientRepository): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $ClientRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt((new \DateTimeImmutable())->modify('+1 hour'));

                $entityManager->persist($user);
                $entityManager->flush();

                // configure PHPMailer
                $mailer->isSMTP();
                $mailer->SMTPAuth = true;
                $mailer->SMTPSecure = 'ssl';
                $mailer->Host = 'smtp.ionos.fr';
                $mailer->Port = 465;
                $mailer->Username = 'ryansifi@librairiedulys.fr';
                $mailer->Password = 'Vk!Gd52!V@Z!g5U';

                $mailer->setFrom('ryansifi@librairiedulys.fr');
                $mailer->addAddress($user->getEmail());
                $mailer->Subject = 'Reset your password';
                $mailer->Body = $this->renderView(
                    'registration/reset_password_email.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                );

                if (!$mailer->send()) {
                    $this->addFlash('danger', 'An error occurred while sending the email.');
                    return $this->redirectToRoute('app_forgot_password');
                }

                $this->addFlash('success', 'An email has been sent to your email address. Please check your inbox.');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'This email is not registered.');

            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('registration/forgot_password.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    #[Route("/reset-password/{token}", name: "app_reset_password")]
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        if (!$token) {
            $this->addFlash('danger', 'Invalid reset password link.');
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(Client::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Invalid reset password link.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getResetTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'Your reset password link has expired. Please try again.');
            return $this->redirectToRoute('app_forgot_password');
        }


        $userData = ['plainPassword' => null, 'confirmPassword' => null];
        $form = $this->createForm(ResetPasswordType::class, $userData);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $password = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);

            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your password has been reset successfully. You can now log in with your new password.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/reset-password.html.twig', [
            'resetPasswordForm' => $form->createView(),
            'token' => $token,
        ]);
    }
}

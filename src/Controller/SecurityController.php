<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // Initialisation de la sécurité
    public function __construct(private Security $security)
    {
        
    }

    // Gestion de la connexion
    #[Route(path: '/login', name: 'app.login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // Redirection si déjà connecté
        if ($this->getUser()) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin.index');
            }
            elseif ($this->security->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('users.index');
            }
        }

        // Récupération des erreurs et du dernier email
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_email' => $lastEmail, 'error' => $error]);
    }

    // Gestion de la déconnexion
    #[Route(path: '/logout', name: 'app.logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator;

/**
 * Contrôleur gérant l'inscription des utilisateurs.
 */
class RegistrationController extends AbstractController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface L'EntityManager pour les opérations de base de données
     */
    private EntityManagerInterface $entity;

    /**
     * Constructeur injectant l'EntityManager.
     *
     * @param EntityManagerInterface $entity L'EntityManager pour les opérations de base de données
     */
    public function __construct(EntityManagerInterface $entity)
    {
        $this->entity = $entity;
    }

    // Section des routes et actions

    /**
     * Gère l'inscription d'un nouvel utilisateur.
     * Affiche le formulaire d'inscription et traite la soumission.
     *
     * @Route('/inscription', name: 'app_inscription', methods: {'GET', 'POST'})
     */
    #[Route('/inscription', name: 'app_inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response {
        // Initialisation de l'utilisateur et du formulaire
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Traitement de la soumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité de l'email
            $existingUser = $this->entity->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new \Symfony\Component\Form\FormError('Cet email est déjà utilisé.'));
            } else {
                // Configuration de l'utilisateur
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
                $user->setImage($this->checkImage($form->get('image')->getData(), $user));

                // Persistance et sauvegarde des données
                $this->entity->persist($user);
                $this->entity->flush();

                // Authentification automatique et redirection
                $userAuthenticator->authenticateUser($user, $authenticator, $request);

                $this->addFlash('success', 'Inscription réussie !');
                return $this->redirectToRoute('app_login');
            }
        }

        // Rendu de la vue avec le formulaire
        return $this->render('registration/inscription.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // Section des méthodes privées

    /**
     * Gère le téléchargement et la validation de l'image de profil.
     *
     * @param UploadedFile|null $file Le fichier image uploadé
     * @param User $user L'utilisateur associé
     * @return string|null Le nom du fichier ou null si aucune image
     */
    private function checkImage(?UploadedFile $file, User $user)
    {
        // Gestion des cas où aucun fichier n'est uploadé
        if (!$file instanceof UploadedFile && $user->getImage() == null) return null;
        elseif (!$file instanceof UploadedFile && $user->getImage() !== null) return $user->getImage();
        else {
            // Génération d'un nom unique et déplacement du fichier
            $fileName = md5(uniqid("user")) . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/image/users/', $fileName);
            return $fileName;
        }
    }
}
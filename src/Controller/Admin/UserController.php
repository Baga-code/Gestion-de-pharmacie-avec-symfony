<?php
// src/Controller/Admin/UserController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\DeleteUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator;

#[Route('/admin/users', name: 'admin.users.')]
class UserController extends AbstractController
{
    private EntityManagerInterface $entity;

    // ------------------------------------------------------------------------
    // Constructeur : injection de l'EntityManager pour toutes les opérations DB
    public function __construct(EntityManagerInterface $entity)
    {
        $this->entity = $entity;
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Liste tous les utilisateurs (accessible uniquement aux admins)
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): Response
    {
        $page    = $request->query->getInt('page', 1);
        $search  = $request->query->get('search', '');
        $limit   = $request->query->getInt('limit', 10);
        $offset  = ($page - 1) * $limit;

        $users = $this->entity
            ->getRepository(User::class)
            ->getAll($offset, $limit, $search);

        return $this->render('admin/user/index.html.twig', [
            'users'    => $users,
            'page'     => $page,
            'limit'    => $limit,
            'search'   => $search,
            'maxPages' => ceil(count($users) / $limit),
            'count'    => $users->count(),
            'iterator' => iterator_count($users),
        ]);
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Création d'un nouvel utilisateur (admin only)
    #[Route('/new', name: 'create', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function register(): Response
    {
        return $this->render('admin/user/register.html.twig');
    }

    #[Route('/new', name: 'store', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function store(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user  = new User();
        $roles = (strtoupper($request->request->get('roles')) === "ADMIN")
               ? ['ROLE_ADMIN']
               : ['ROLE_USER'];

        $user
            ->setNom($request->request->get('nom'))
            ->setPrenom($request->request->get('prenom'))
            ->setEmail($request->request->get('email'))
            ->setRoles($roles)
            ->setPassword($hasher->hashPassword($user, $request->request->get('password')))
            ->setAdresse($request->request->get('adresse'))
            ->setTelephone($request->request->get('telephone'))
            ->setImage($this->checkImage($request->files->get('image'), $user))
        ;

        $this->entity->persist($user);
        $this->entity->flush();

        $this->addFlash('success', 'Nouvel utilisateur créé avec succès');
        return $this->redirectToRoute('admin.users.index');
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Inscription publique (ROLE_USER)
    #[Route('/inscription', name: 'app_inscription', methods: ['GET', 'POST'])]
    public function publicInscription(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user
                ->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setEmail($request->request->get('email'))
                ->setRoles(['ROLE_USER'])
                ->setPassword($hasher->hashPassword($user, $request->request->get('password')))
                ->setAdresse($request->request->get('adresse'))
                ->setTelephone($request->request->get('telephone'))
                ->setImage($this->checkImage($request->files->get('image'), $user));

            $this->entity->persist($user);
            $this->entity->flush();

            // Authentification automatique
            $userAuthenticator->authenticateUser($user, $authenticator, $request);

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app.login');
        }

        return $this->render('admin/user/inscription.html.twig');
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Affiche le formulaire d'édition pour un utilisateur donné
    #[Route('/edit/{id}', name: 'edit', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(User $user): Response
    {
        $current = $this->getUser();

        // 1) Si vous n’êtes pas admin ET ce n’est pas votre profil → interdit
        if (
            !in_array('ROLE_ADMIN', $current->getRoles(), true)
            && $current->getId() !== $user->getId()
        ) {
            throw new AccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        // 2) Si vous êtes admin et que la cible est admin (différent de vous) → interdit
        if (
            in_array('ROLE_ADMIN', $current->getRoles(), true)
            && in_array('ROLE_ADMIN', $user->getRoles(), true)
            && $current->getId() !== $user->getId()
        ) {
            throw new AccessDeniedException('Un administrateur ne peut modifier un autre administrateur.');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
        ]);
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Traite l’envoi du formulaire d’édition
    #[Route('/edit/{id}', name: 'update', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(User $user, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $current = $this->getUser();

        // Même logique de sécurité qu’en GET
        if (
            !in_array('ROLE_ADMIN', $current->getRoles(), true)
            && $current->getId() !== $user->getId()
        ) {
            throw new AccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }
        if (
            in_array('ROLE_ADMIN', $current->getRoles(), true)
            && in_array('ROLE_ADMIN', $user->getRoles(), true)
            && $current->getId() !== $user->getId()
        ) {
            throw new AccessDeniedException('Un administrateur ne peut modifier un autre administrateur.');
        }

        // Mise à jour des données
        $roles = (strtoupper($request->request->get('roles')) === "ADMIN")
               ? ['ROLE_ADMIN']
               : ['ROLE_USER'];

        $user
            ->setNom($request->request->get('nom'))
            ->setPrenom($request->request->get('prenom'))
            ->setEmail($request->request->get('email'))
            ->setAdresse($request->request->get('adresse'))
            ->setTelephone($request->request->get('telephone'))
            ->setRoles($roles)
            ->setPassword($hasher->hashPassword($user, $request->request->get('password')))
            ->setImage($this->checkImage($request->files->get('image'), $user))
        ;

        $this->entity->flush();

        $this->addFlash('success', sprintf(
            'Utilisateur N°%d mis à jour avec succès',
            $user->getId()
        ));

        return $this->redirectToRoute('admin.users.profil', ['id' => $user->getId()]);
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Supprime un utilisateur (et son image), dispatch d’événement
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, EventDispatcherInterface $dispatcher): Response
    {
        $this->deleteImage($user);
        $dispatcher->dispatch(new DeleteUserEvent($user));
        $this->entity->remove($user);
        $this->entity->flush();
        $this->addFlash('danger', 'Utilisateur supprimé');
        return $this->redirectToRoute('admin.users.index');
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Affiche le profil d’un utilisateur donné
    #[Route('/profil/{id}', name: 'profil', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function profil(User $user): Response
    {
        return $this->render('admin/user/profil.html.twig', [
            'user' => $user,
        ]);
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Gestion de l’upload et suppression d’images
    private function checkImage(?UploadedFile $file, User $user): ?string
    {
        if (!$file instanceof UploadedFile && $user->getImage() === null) {
            return null;
        } elseif (!$file instanceof UploadedFile) {
            return $user->getImage();
        } else {
            $this->deleteImage($user);
            $fileName = md5(uniqid('user', true)) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/image/users/',
                $fileName
            );
            return $fileName;
        }
    }

    private function deleteImage(User $user): void
    {
        if ($user->getImage() !== null) {
            $path = $this->getParameter('kernel.project_dir')
                  . '/public/image/users/' . $user->getImage();
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    // ------------------------------------------------------------------------
}

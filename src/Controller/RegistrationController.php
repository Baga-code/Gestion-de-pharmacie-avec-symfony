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

class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entity)
    {
        
    }

    #[Route('/inscription', name: 'app_inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité de l'email via le formulaire
            $existingUser = $this->entity->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new \Symfony\Component\Form\FormError('Cet email est déjà utilisé.'));
            } else {
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
                $user->setImage($this->checkImage($form->get('image')->getData(), $user));

                $this->entity->persist($user);
                $this->entity->flush();

                $userAuthenticator->authenticateUser($user, $authenticator, $request);

                $this->addFlash('success', 'Inscription réussie !');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/inscription.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function checkImage(?UploadedFile $file, User $user)
    {
        if (!$file instanceof UploadedFile && $user->getImage() == null) return null;
        elseif (!$file instanceof UploadedFile && $user->getImage() !== null) return $user->getImage();
        else {
            $fileName = md5(uniqid("user")) . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/image/users/', $fileName);
            return $fileName;
        }
    }
}
<?php

namespace App\Controller\Users;

use App\Entity\Vente;
use Doctrine\ORM\EntityManagerInterface; // Correction de l'import
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users/purchases', name: 'users.purchases.')]
#[IsGranted('ROLE_USER')]
class VenteController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entity) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $purchases = $this->entity->getRepository(Vente::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('users/purchases/index.html.twig', [
            'purchases' => $purchases,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Vente $vente): Response
    {
        if ($vente->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('users/purchases/show.html.twig', [
            'vente' => $vente,
        ]);
    }
}

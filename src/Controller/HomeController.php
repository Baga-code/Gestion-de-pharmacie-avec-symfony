<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Medicament;
use App\Entity\User;
use App\Entity\Vente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    // ------------------------------------------------------------------------
    // Constructeur : injection de l'EntityManager pour accès aux repos
    public function __construct(private EntityManagerInterface $entity)
    {
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Dashboard admin : compteurs et graphique de stock
    #[Route('/', name: 'admin.index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response
    {
        // → Compteurs existants
        $medicaments = $this->entity->getRepository(Medicament::class)->medicamentNumber();
        $categories  = $this->entity->getRepository(Category::class)->categoryNumber();
        $users       = $this->entity->getRepository(User::class)->userNumber();

        // → Récupération des ventes et calcul du total
        $lastVentes = $this->entity->getRepository(Vente::class)->getSomme();
        $total = array_reduce($lastVentes, fn($sum, $v) => $sum + $v['total'], 0);
        $ventes = $this->entity->getRepository(Vente::class)->getLastVente();

        // → NOUVEAU : statistiques de stock pour Chart.js
        $stats  = $this->entity
                       ->getRepository(Medicament::class)
                       ->getStockStats();

        $labels = array_map(fn($s) => $s['produit'], $stats);
        $stocks = array_map(fn($s) => $s['stock'],   $stats);
        // Génération dynamique de couleurs RGBA
       $colors = array_map(
          fn() => sprintf('rgba(%d,%d,%d,0.7)', rand(0,255), rand(0,255), rand(0,255)),
          $stats
          );


        // → Passage des données au template
        return $this->render('admin/home.html.twig', [
            'medicaments' => $medicaments,
            'categories'  => $categories,
            'users'       => $users,
            'total'       => $total,
            'ventes'      => $ventes,
            'labels'      => json_encode($labels),
            'stocks'      => json_encode($stocks),
            'colors'      => json_encode($colors),
        ]);
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Dashboard utilisateur simple (ROLE_USER) sans graphique
    #[Route('/users', name: 'users.index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function user(): Response
    {
        // Réutilisation des mêmes compteurs et ventes
        $medicaments = $this->entity->getRepository(Medicament::class)->medicamentNumber();
        $categories  = $this->entity->getRepository(Category::class)->categoryNumber();
        $users       = $this->entity->getRepository(User::class)->userNumber();

        $lastVentes = $this->entity->getRepository(Vente::class)->getSomme();
        $total = array_reduce($lastVentes, fn($sum, $v) => $sum + $v['total'], 0);
        $ventes = $this->entity->getRepository(Vente::class)->getLastVente();

        return $this->render('users/home.html.twig', [
            'medicaments' => $medicaments,
            'categories'  => $categories,
            'users'       => $users,
            'total'       => $total,
            'ventes'      => $ventes,
        ]);
    }
    // ------------------------------------------------------------------------
}

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

/**
 * Contrôleur principal pour la gestion des pages d'accueil admin et utilisateur.
 */
class HomeController extends AbstractController
{
    // Déclaration de l'EntityManager comme propriété privée
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
     * Page d'accueil pour l'administrateur.
     * Affiche les statistiques globales et les dernières ventes.
     *
     * @Route('/', name: 'admin.index')
     */
    #[Route('/', name: 'admin.index')]
    public function admin(): Response
    {
        // Récupération des compteurs pour les entités principales
        $medicaments = $this->entity->getRepository(Medicament::class)->count([]);
        $categories = $this->entity->getRepository(Category::class)->count([]);
        $users = $this->entity->getRepository(User::class)->count([]);

        // Calcul du total des ventes
        $total = $this->entity->createQueryBuilder('v')
            ->select('SUM(v.total)')
            ->from(Vente::class, 'v')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Récupération des 5 dernières ventes
        $ventes = $this->entity->getRepository(Vente::class)->findBy(
            [],
            ['createdAt' => 'DESC'],
            5
        );

        // Préparation des données pour le graphique
        $data = $this->entity->getRepository(Medicament::class)->findAll();
        $labels = array_map(fn($m) => $m->getNom(), $data);
        $stocks = array_map(fn($m) => $m->getNombre(), $data);
        $colors = array_map(fn($i) => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), range(1, count($data)));

        // Rendu de la vue avec les données
        return $this->render('admin/home.html.twig', [
            'medicaments' => $medicaments,
            'categories' => $categories,
            'users' => $users,
            'total' => $total,
            'ventes' => $ventes,
            'labels' => json_encode($labels),
            'stocks' => json_encode($stocks),
            'colors' => json_encode($colors),
        ]);
    }

    /**
     * Page d'accueil pour les utilisateurs.
     * Affiche les statistiques globales et les derniers achats de l'utilisateur connecté.
     *
     * @Route('/users', name: 'users.index')
     */
    #[Route('/users', name: 'users.index')]
    public function users(): Response
    {
        // Récupération des compteurs pour les entités principales
        $medicaments = $this->entity->getRepository(Medicament::class)->count([]);
        $categories = $this->entity->getRepository(Category::class)->count([]);
        $users = $this->entity->getRepository(User::class)->count([]);

        // Récupération des 5 derniers achats de l'utilisateur connecté
        $purchases = $this->entity->getRepository(Vente::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC'],
            5
        );

        // Calcul du total des achats
        $total = array_sum(array_map(fn($v) => $v->getTotal(), $purchases));

        // Rendu de la vue avec les données
        return $this->render('users/home.html.twig', [
            'medicaments' => $medicaments,
            'categories' => $categories,
            'users' => $users,
            'purchases' => $purchases,
            'total' => $total,
        ]);
    }
}
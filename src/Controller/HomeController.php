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

class HomeController extends AbstractController
{
    private EntityManagerInterface $entity;

    public function __construct(EntityManagerInterface $entity)
    {
        $this->entity = $entity;
    }

    #[Route('/', name: 'admin.index')]
    public function admin(): Response
    {
        $medicaments = $this->entity->getRepository(Medicament::class)->count([]);
        $categories = $this->entity->getRepository(Category::class)->count([]);
        $users = $this->entity->getRepository(User::class)->count([]);

        $total = $this->entity->createQueryBuilder('v')
            ->select('SUM(v.total)')
            ->from(Vente::class, 'v')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        $ventes = $this->entity->getRepository(Vente::class)->findBy(
            [],
            ['createdAt' => 'DESC'],
            5
        );

        $data = $this->entity->getRepository(Medicament::class)->findAll();
        $labels = array_map(fn($m) => $m->getNom(), $data);
        $stocks = array_map(fn($m) => $m->getNombre(), $data);
        $colors = array_map(fn($i) => sprintf('#%06X', mt_rand(0, 0xFFFFFF)), range(1, count($data)));

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

    #[Route('/users', name: 'users.index')]
    public function users(): Response
    {
        $medicaments = $this->entity->getRepository(Medicament::class)->count([]);
        $categories = $this->entity->getRepository(Category::class)->count([]);
        $users = $this->entity->getRepository(User::class)->count([]);

        $purchases = $this->entity->getRepository(Vente::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC'],
            5
        );

        $total = array_sum(array_map(fn($v) => $v->getTotal(), $purchases));

        return $this->render('users/home.html.twig', [
            'medicaments' => $medicaments,
            'categories' => $categories,
            'users' => $users,
            'purchases' => $purchases,
            'total' => $total,
        ]);
    }
}
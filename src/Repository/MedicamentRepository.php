<?php

namespace App\Repository;

use App\Entity\Medicament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medicament>
 *
 * Repository : gestion des entités Medicament
 */
class MedicamentRepository extends ServiceEntityRepository
{
    /**
     * Constructeur : injection du ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicament::class);
    }

    // ------------------------------------------------------------------------
    // Recherche de produits par nom (stock > 0)
    public function getWithSearch(string $search): array
    {
        return $this->createQueryBuilder('m')
                    ->where('m.nom LIKE :search')
                    ->andWhere('m.nombre > 0')
                    ->setParameter('search', '%'.$search.'%')
                    ->getQuery()
                    ->getResult();
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Pagination et filtrage (nom + catégorie)
    public function getAll(int $offset, int $limit, string $search, int $category): Paginator
    {
        $qb = $this->createQueryBuilder('m')
                   ->select('m', 'c')
                   ->leftJoin('m.category', 'c')
                   ->where('m.nom LIKE :search')
                   ->setFirstResult($offset)
                   ->setMaxResults($limit)
                   ->setParameter('search', '%'.$search.'%');

        if ($category !== 1000) {
            $qb->andWhere('c.id = :category')
               ->setParameter('category', $category);
        }

        return new Paginator(
            $qb->getQuery()
               ->setHint(Paginator::HINT_ENABLE_DISTINCT, true)
        );
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Récupération des produits pour une vente donnée
    public function getMedInVente(array $data = []): array
    {
        return $this->getEntityManager()
                    ->createQuery(
                        'SELECT m
                         FROM App\\Entity\\Medicament m
                         WHERE m.id IN (:data)'
                    )
                    ->setParameter('data', $data)
                    ->getResult();
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // Nombre total de médicaments
    public function medicamentNumber(): int
    {
        return (int) $this->createQueryBuilder('m')
                          ->select('COUNT(m)')
                          ->getQuery()
                          ->getSingleScalarResult();
    }
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // NOUVEAU : statistiques de stock pour Chart.js
    /**
     * Retourne un tableau de la forme :
     * [
     *   ['produit' => 'Paracétamol', 'stock' => 12],
     *   ['produit' => 'Ibuprofène', 'stock' => 7],
     *   …
     * ]
     *
     * @return array<int, array{produit: string, stock: int}>
     */
    public function getStockStats(): array
    {
        return $this->createQueryBuilder('m')
                    ->select('m.nom AS produit', 'm.nombre AS stock')
                    ->getQuery()
                    ->getArrayResult();
    }
    // ------------------------------------------------------------------------
}

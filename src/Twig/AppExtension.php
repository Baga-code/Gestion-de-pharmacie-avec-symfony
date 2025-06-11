<?php

namespace App\Twig;

use App\Entity\Approval;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(private EntityManagerInterface $entity) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('approval_status', [$this, 'getApprovalStatus']),
        ];
    }

    public function getApprovalStatus(Cart $cart): Approval
    {
        $approval = $this->entity->getRepository(Approval::class)->findOneBy(['cart' => $cart]);
        return $approval ?? new Approval(); // Retourne un Approval vide si aucun n’existe
    }
}
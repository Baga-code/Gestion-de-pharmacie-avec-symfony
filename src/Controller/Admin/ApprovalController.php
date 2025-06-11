<?php

namespace App\Controller\Admin;

use App\Entity\Approval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/approvals', name: 'admin.approvals.')]
#[IsGranted('ROLE_ADMIN')]
class ApprovalController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entity) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $approvals = $this->entity->getRepository(Approval::class)->findBy(['status' => 'pending'], ['createdAt' => 'ASC']);

        return $this->render('admin/approvals/index.html.twig', [
            'approvals' => $approvals,
        ]);
    }

    #[Route('/approve/{id}', name: 'approve', methods: ['POST'])]
    public function approve(Approval $approval): Response
    {
        if ($approval->getStatus() !== 'pending') {
            $this->addFlash('danger', 'Demande déjà traitée.');
            return $this->redirectToRoute('admin.approvals.index');
        }

        $approval->setAdmin($this->getUser())
                 ->setStatus('approved');
        $this->entity->persist($approval);
        $this->entity->flush();

        $this->addFlash('success', 'Demande approuvée.');
        return $this->redirectToRoute('admin.approvals.index');
    }

    #[Route('/reject/{id}', name: 'reject', methods: ['POST'])]
    public function reject(Approval $approval): Response
    {
        if ($approval->getStatus() !== 'pending') {
            $this->addFlash('danger', 'Demande déjà traitée.');
            return $this->redirectToRoute('admin.approvals.index');
        }

        $approval->setAdmin($this->getUser())
                 ->setStatus('rejected');
        $this->entity->persist($approval);
        $this->entity->flush();

        $this->addFlash('success', 'Demande rejetée.');
        return $this->redirectToRoute('admin.approvals.index');
    }
}
<?php

namespace App\Controller\Users;

use App\Entity\Approval;
use App\Entity\Cart;
use App\Entity\Medicament;
use App\Entity\Vente;
use App\Event\StoreVenteEvent;
use App\Event\UpdateMedicamentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users/cart', name: 'users.cart.')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entity) {}

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $medicamentId = $request->request->getInt('medicament_id');
        $quantities = $request->request->all('quantities');
        $quantity = isset($quantities[$medicamentId]) ? (int)$quantities[$medicamentId] : 1;

        $medicament = $this->entity->getRepository(Medicament::class)->find($medicamentId);
        if (!$medicament || $medicament->getNombre() < $quantity) {
            $this->addFlash('danger', 'Médicament non disponible ou quantité insuffisante.');
            return $this->redirectToRoute('users.cart.view');
        }

        // Vérifier si le produit existe déjà dans le panier
        $existingCart = $this->entity->getRepository(Cart::class)->findOneBy([
            'user' => $this->getUser(),
            'medicament' => $medicament,
        ]);

        if ($existingCart) {
            // Incrémenter la quantité si le produit existe
            $newQuantity = $existingCart->getQuantity() + $quantity;
            if ($medicament->getNombre() >= $newQuantity) {
                $existingCart->setQuantity($newQuantity);
                $this->entity->persist($existingCart);
                $this->entity->flush();

                $message = $medicament->isOrdonnance()
                    ? 'Quantité mise à jour. En attente d’approbation.'
                    : 'Quantité mise à jour dans le panier.';
                $this->addFlash('success', $message);
            } else {
                $this->addFlash('danger', 'Quantité insuffisante en stock.');
            }
        } else {
            // Créer une nouvelle entrée si le produit n'existe pas
            $cart = new Cart();
            $cart->setUser($this->getUser())
                 ->setMedicament($medicament)
                 ->setQuantity($quantity)
                 ->setRequiresApproval($medicament->isOrdonnance());

            $this->entity->persist($cart);

            if ($medicament->isOrdonnance()) {
                $approval = new Approval();
                $approval->setCart($cart)
                         ->setStatus('pending');
                $this->entity->persist($approval);
            }

            $this->entity->flush();

            $message = $medicament->isOrdonnance()
                ? 'Médicament ajouté au panier. En attente d’approbation.'
                : 'Médicament ajouté au panier.';
            $this->addFlash('success', $message);
        }

        return $this->redirectToRoute('users.cart.view');
    }

    #[Route('/view', name: 'view', methods: ['GET'])]
    public function view(): Response
    {
        $cartItems = $this->entity->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);
        $total = array_sum(array_map(fn($item) => $item->getMedicament()->getPrix() * $item->getQuantity(), $cartItems));

        $approvals = [];
        foreach ($cartItems as $item) {
            if ($item->getRequiresApproval()) {
                $approval = $this->entity->getRepository(Approval::class)->findOneBy(['cart' => $item]);
                $approvals[$item->getId()] = $approval;
            }
        }

        return $this->render('users/cart/view.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
            'approvals' => $approvals,
        ]);
    }

    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(EventDispatcherInterface $dispatcher): Response
    {
        $cartItems = $this->entity->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);
        if (empty($cartItems)) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('users.cart.view');
        }

        foreach ($cartItems as $item) {
            if ($item->getRequiresApproval()) {
                $approval = $this->entity->getRepository(Approval::class)->findOneBy(['cart' => $item]);
                if (!$approval || $approval->getStatus() !== 'approved') {
                    $this->addFlash('danger', 'Certains médicaments nécessitent une approbation médicale.');
                    return $this->redirectToRoute('users.cart.view');
                }
            }
        }

        $total = array_sum(array_map(fn($item) => $item->getMedicament()->getPrix() * $item->getQuantity(), $cartItems));
        $vente = new Vente();
        $vente->setTotal($total)
              ->setUser($this->getUser())
              ->setCreatedAt(new \DateTimeImmutable());

        $this->entity->persist($vente);
        $this->entity->flush();

        $data = [];
        foreach ($cartItems as $item) {
            $data[$item->getMedicament()->getId()] = $item->getQuantity();
            $this->entity->remove($item);
        }

        $this->entity->flush();

        $dispatcher->dispatch(new StoreVenteEvent($vente, $data));
        $dispatcher->dispatch(new UpdateMedicamentEvent($data));

        $this->addFlash('success', 'Achat effectué avec succès.');
        return $this->redirectToRoute('users.purchases.index');
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['POST'])]
    public function remove(Cart $cart): Response
    {
        if ($cart->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Action non autorisée.');
            return $this->redirectToRoute('users.cart.view');
        }

        $approval = $this->entity->getRepository(Approval::class)->findOneBy(['cart' => $cart]);
        if ($approval) {
            $this->entity->remove($approval);
        }

        $this->entity->remove($cart);
        $this->entity->flush();

        $this->addFlash('success', 'Médicament retiré du panier.');
        return $this->redirectToRoute('users.cart.view');
    }
}
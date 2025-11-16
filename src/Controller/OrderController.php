<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation logic for customer restrictions
            $phone = $order->getPhone();
            
            if ($phone) {
                // Check if customer already has a subscription agreement
                $existingSubscription = $entityManager->getRepository(Order::class)
                    ->findOneBy([
                        'phone' => $phone,
                        'type' => 'subscription'
                    ]);
                
                if ($existingSubscription) {
                    $this->addFlash('error', 'Customer can have at most one subscription agreement.');
                    return $this->render('order/new.html.twig', [
                        'order' => $order,
                        'form' => $form,
                    ]);
                }
                
                // Check if customer already purchased a unique item
                $existingItemPurchase = $entityManager->getRepository(Order::class)
                    ->findOneBy([
                        'phone' => $phone,
                        'type' => 'item',
                        'productId' => $order->getProductId()
                    ]);
                
                if ($existingItemPurchase) {
                    $this->addFlash('error', 'Customer can purchase each unique item only once.');
                    return $this->render('order/new.html.twig', [
                        'order' => $order,
                        'form' => $form,
                    ]);
                }
            }

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation logic for customer restrictions
            $phone = $order->getCustomerPhoneNumber();

            if ($phone) {
                // Check if customer already has a subscription agreement
                $existingSubscription = $entityManager->getRepository(Order::class)
                    ->findOneBy([
                        'customerPhoneNumber' => $phone,
                        'subscriptionPackage' => $order->getSubscriptionPackage() ? $order->getSubscriptionPackage()->getId() : null
                    ]);

                if ($existingSubscription && $order->getSubscriptionPackage()) {
                    $this->addFlash('error', 'Customer can have at most one subscription agreement.');
                    return $this->render('order/new.html.twig', [
                        'order' => $order,
                        'form' => $form,
                    ]);
                }

                // Check if customer already purchased an article.
                if ($order->getArticles()->count() > 0) {
                    $existingArticlePurchase = $entityManager->getRepository(Order::class)
                        ->findOneBy([
                            'customerPhoneNumber' => $phone,
                            'articles' => $order->getArticles()->first()
                        ]);
                    
                    if ($existingArticlePurchase) {
                        $this->addFlash('error', 'Customer can purchase each unique item only once.');
                        return $this->render('order/new.html.twig', [
                            'order' => $order,
                            'form' => $form,
                        ]);
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
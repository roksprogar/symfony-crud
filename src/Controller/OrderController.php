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
                    return $this->render('order/edit.html.twig', [
                        'order' => $order,
                        'form' => $form,
                    ]);
                }

                // Check if customer already purchased an article.
                // Fix: properly handle article validation in edit mode
                if ($order->getArticles()->count() > 0) {
                    // For edit mode, we need to check against all orders for this customer
                    $existingOrders = $entityManager->getRepository(Order::class)
                        ->findBy(['customerPhoneNumber' => $phone]);
                    
                    foreach ($existingOrders as $existingOrder) {
                        // Skip the current order being edited
                        if ($existingOrder->getId() === $order->getId()) {
                            continue;
                        }
                        
                        // Check if any of the articles in existing orders match those in current order
                        foreach ($existingOrder->getArticles() as $existingArticle) {
                            if ($order->getArticles()->contains($existingArticle)) {
                                $this->addFlash('error', 'Customer can purchase each unique item only once.');
                                return $this->render('order/edit.html.twig', [
                                    'order' => $order,
                                    'form' => $form,
                                ]);
                            }
                        }
                    }
                }
            }

            // Ensure proper flushing of the entity
            try {
                $entityManager->flush();
            } catch (\Exception $e) {
                // Log the exception for debugging purposes
                error_log('Flush error: ' . $e->getMessage());
                throw $e;
            }

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
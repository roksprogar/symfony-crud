<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\SubscriptionPackage;
use App\Form\SubscriptionPackageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SubscriptionPackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/subscription-package')]
class SubscriptionPackageController extends AbstractController
{
    #[Route('/', name: 'app_subscription_package_index', methods: ['GET'])]
    public function index(SubscriptionPackageRepository $subscriptionPackageRepository): Response
    {
        return $this->render('subscription_package/index.html.twig', [
            'subscription_packages' => $subscriptionPackageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subscription_package_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subscriptionPackage = new SubscriptionPackage();
        $form = $this->createForm(SubscriptionPackageType::class, $subscriptionPackage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subscriptionPackage);
            $entityManager->flush();

            return $this->redirectToRoute('app_subscription_package_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription_package/new.html.twig', [
            'subscription_package' => $subscriptionPackage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_package_show', methods: ['GET'])]
    public function show(SubscriptionPackage $subscriptionPackage): Response
    {
        return $this->render('subscription_package/show.html.twig', [
            'subscription_package' => $subscriptionPackage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subscription_package_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubscriptionPackage $subscriptionPackage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriptionPackageType::class, $subscriptionPackage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subscription_package_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription_package/edit.html.twig', [
            'subscription_package' => $subscriptionPackage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subscription_package_delete', methods: ['POST'])]
    public function delete(Request $request, SubscriptionPackage $subscriptionPackage, EntityManagerInterface $entityManager): Response
    {
        // Check if any orders exist for this subscription package
        $ordersContainingSubscriptionPackage = $entityManager->getRepository(Order::class)->findBy([
            'subscriptionPackage' => $subscriptionPackage
        ]);

        if (count($ordersContainingSubscriptionPackage) > 0) {
            $this->addFlash('error', 'Cannot delete subscription package because it has existing orders.');
            return $this->redirectToRoute('app_subscription_package_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$subscriptionPackage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subscriptionPackage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subscription_package_index', [], Response::HTTP_SEE_OTHER);
    }
}
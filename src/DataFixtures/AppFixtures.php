<?php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\SubscriptionPackage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class AppFixtures extends Fixture
{
    /**
     * Loads sample entities into the database.
     */
    public function load(ObjectManager $manager): void
    {
        // Load SubscriptionPackage entities
        $packagesData = [
            ['Basic Package', 'Essential subscription with core features.', '19.99', true, '2023-01-15 10:00:00'],
            ['Premium Package', 'Complete subscription with all features.', '49.99', true, '2023-02-20 14:30:00'],
            ['Starter Package', 'Entry-level subscription for beginners.', '9.99', false, '2023-03-10 09:15:00'],
        ];

        $packages = [];
        foreach ($packagesData as $index => $data) {
            $package = new SubscriptionPackage();
            $package->setName($data[0]);
            $package->setDescription($data[1]);
            $package->setPrice($data[2]);
            $package->setIncludesMonthlyMagazine($data[3]);
            $package->setDateCreated(new DateTime($data[4]));

            $manager->persist($package);
            $packages[] = $package;
        }

        // Load Article entities
        $articlesData = [
            ['Sample Article 1', 'Description for sample article 1.', '29.99', 'supplier1@example.com', '2023-01-15 10:00:00'],
            ['Product Name 2', 'Detailed description of product 2 with more content.', '59.50', 'supplier2@example.com', '2023-02-20 14:30:00'],
            ['Tech Gadget', 'Latest tech gadget with amazing features.', '199.99', 'supplier3@example.com', '2023-03-10 09:15:00'],
            ['Home & Kitchen', 'Everything you need for your home.', '89.75', 'supplier4@example.com', '2023-04-05 16:45:00'],
            ['Fashion Item', 'Trendy fashion piece for every season.', '45.25', 'supplier5@example.com', '2023-05-12 11:20:00'],
            ['Sports Equipment', 'High-quality sports gear for enthusiasts.', '129.99', 'supplier6@example.com', '2023-06-18 13:50:00'],
            ['Books & Media', 'Collection of best-selling books and media.', '35.00', 'supplier7@example.com', '2023-07-22 15:35:00'],
            ['Electronics', 'Latest electronics at competitive prices.', '299.99', 'supplier8@example.com', '2023-08-30 08:40:00'],
            ['Toys & Games', 'Fun toys and games for all ages.', '24.99', 'supplier9@example.com', '2023-09-14 12:25:00'],
            ['Beauty Products', 'Premium beauty products for daily use.', '67.50', 'supplier10@example.com', '2023-10-25 17:10:00'],
        ];

        $articles = [];
        foreach ($articlesData as $index => $data) {
            $article = new Article();
            $article->setName($data[0]);
            $article->setDescription($data[1]);
            $article->setPrice($data[2]);
            $article->setSupplierEmail($data[3]);
            $article->setDateCreated(new DateTime($data[4]));

            $manager->persist($article);
            $articles[] = $article;
        }

        // Load Order entities
        $ordersData = [
            ['ORD-001', '1234567890', 'pending', '49.98', [], [0]],
            ['ORD-002', '0987654321', 'completed', '129.99', [0], [1]],
            ['ORD-003', '5555555555', 'processing', '89.75', [1, 2], []],
            ['ORD-004', '1111111111', 'completed', '39.99', [3], [0]],
            ['ORD-005', '2222222222', 'pending', '149.97', [4, 5, 6], [2]],
            ['ORD-006', '3333333333', 'completed', '29.99', [7], []],
            ['ORD-007', '4444444444', 'processing', '199.99', [8, 9], [0]],
            ['ORD-008', '6666666666', 'completed', '67.50', [2], []],
            ['ORD-009', '7777777777', 'pending', '159.98', [0, 1], [1]],
            ['ORD-010', '8888888888', 'completed', '299.99', [3, 4, 5], [2]],
        ];

        foreach ($ordersData as $data) {
            $order = new Order();
            $order->setOrderNumber($data[0]);
            $order->setCustomerPhoneNumber($data[1]);
            $order->setOrderStatus($data[2]);
            $order->setPrice($data[3]);
            $order->setDateCreated(new DateTime('2023-01-01 00:00:00'));

            // Add articles to order
            foreach ($data[4] as $articleIndex) {
                if (isset($articles[$articleIndex])) {
                    $order->addArticle($articles[$articleIndex]);
                }
            }

            // Set subscription package
            if (isset($data[5]) && count($data[5]) > 0) {
                $packageIndex = $data[5][0];
                if (isset($packages[$packageIndex])) {
                    $order->setSubscriptionPackage($packages[$packageIndex]);
                }
            }

            $manager->persist($order);
        }

        $manager->flush();
    }
}
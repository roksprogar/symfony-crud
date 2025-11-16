<?php
// src/Entity/Order.php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`orders`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $customerPhoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $orderStatus = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'orders', cascade: ['persist'])]
    private Collection $articles;

    #[ORM\ManyToOne(targetEntity: SubscriptionPackage::class, inversedBy: 'orders')]
    private ?SubscriptionPackage $subscriptionPackage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getCustomerPhoneNumber(): ?string
    {
        return $this->customerPhoneNumber;
    }

    public function setCustomerPhoneNumber(string $customerPhoneNumber): static
    {
        $this->customerPhoneNumber = $customerPhoneNumber;
        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): static
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSubscriptionPackage(): ?SubscriptionPackage
    {
        return $this->subscriptionPackage;
    }

    public function setSubscriptionPackage(?SubscriptionPackage $subscriptionPackage): static
    {
        $this->subscriptionPackage = $subscriptionPackage;
        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            // Note: Article entity manages the relationship, so we don't call setOrder here
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // Article entity manages the relationship, so we don't call setOrder(null) here
        }

        return $this;
    }
}

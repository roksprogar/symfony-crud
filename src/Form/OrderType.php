<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderNumber', TextType::class, [
                'label' => 'Order Number'
            ])
            ->add('customerPhoneNumber', TextType::class, [
                'label' => 'Customer Phone Number'
            ])
            ->add('orderStatus', ChoiceType::class, [
                'label' => 'Order Status',
                'choices' => [
                    'Pending' => 'pending',
                    'Processing' => 'processing',
                    'Shipped' => 'shipped',
                    'Delivered' => 'delivered',
                    'Cancelled' => 'cancelled'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'scale' => 2
            ])
            ->add('dateCreated', DateTimeType::class, [
                'label' => 'Date Created',
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
<?php

namespace App\Form;

use App\Entity\Commande;
<<<<<<< HEAD
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RadioType;

=======

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\RadioType;


>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('AdresseLivraison')
            ->add('prixCommande')
<<<<<<< HEAD
            ->add('methodePaiement', RadioType::class, [
                'label' => 'Payment Method',
                'expanded' => true,
                'choices' => [
                    'Check' => 'check',
                    'Credit Card' => 'credit_card',
                    'Cash on Delivery' => 'cod',
                ],
            ])
            // ->add('methodePaiement', RadioType::class, [
            //     'label' => 'Méthode de paiement',
            //     'expanded' => true,
            //     'choices' => [
            //         'Chèque' => 'check',
            //         'Carte bancaire' => 'credit_card',
            //         'Livraison' => 'delivery',
            //     ],
            //     'required' => true,
            // ])
            // ->add('methodePaiement', RadioType::class, [
            //     // 'label' => 'Mon bouton radio',
            //     'expanded' => true,
            //     'multiple' => false,
            //     'choices' => [
            //         'Option 1' => 'paiement par chèque',
            //         'Option 2' => 'paiement par carte bancaire',
            //         'Option 3' => 'paiement à la livraison',
            //     ],
            // ])
=======
            ->add('methodePaiement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'paiement par chèque' => 'Chèque',
                    'paiement par carte bancaire' => 'Carte bancaire',
                    'paiement à la livraison' => 'à la livraison',
                ],
                'expanded' => false,
                'multiple' => false,

            ])

>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
            ->add('telephone');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RadioType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('AdresseLivraison')
            ->add('prixCommande')
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
            ->add('telephone');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Abonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
<<<<<<< HEAD
=======
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< HEAD
            ->add('nomAbonnement')
            ->add('prixAbonnement')
            ->add('dureeAbonnement')
=======
            ->add('nomAbonnement', ChoiceType::class, [
                'label' => 'Nom de l\'abonnement ',
                'choices' => [
                    'Basic' => 'Basic',
                    'Pro' => 'Pro',
                    'Premium' => 'Premium',
                ],
                'expanded' => true,
                'multiple' => false,

            ])
            ->add('prixAbonnement')
            ->add('dureeAbonnement', ChoiceType::class, [
                'label' => 'Durée de l\'abonnement ',
                'choices' => [
                    'Mensuel' => 'Mensuel',
                    'Annuel' => 'Annuel',
                ],
                'expanded' => true,
                'multiple' => false,

            ])
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}

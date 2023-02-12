<?php

namespace App\Form;

use App\Entity\Activite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomAcitivite',null,array('label' => false))
            ->add('descriptionActivite',null,array('label' => false))
            ->add('dureeActivite',null,array('label' => false))
            ->add('DateActivite',null,array('label' => false))
            ->add('coach',null,array('label' => false))
            ->add('nbrePlace',null,array('label' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}

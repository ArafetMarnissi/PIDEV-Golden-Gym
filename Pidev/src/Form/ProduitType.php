<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('prixProduit')
            ->add('quantiteProduit')
            //->add('imageProduit')
            ->add('imageProduit', FileType::class, [
                'label' => 'image seulement',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        //  'mimeTypes' => [
                        //     'imageProduit/gif',
                        //     'imageProduit/jpeg',
                        //     'imageProduit/jpg',
                        //     'imageProduit/png',
                        //  ],
                        'mimeTypesMessage' => 'merci de telecharger une photo valide',
                        ]),
                ],
            ])
            ->add('dateExpiration')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nomCategory',
                'multiple' => false,
                'expanded' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

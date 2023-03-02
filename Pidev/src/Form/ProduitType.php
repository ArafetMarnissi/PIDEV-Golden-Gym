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
                'label' => 'image only',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
<<<<<<< HEAD
                        'mimeTypes' => [
                            'application/jpeg',
                            'application/jpg',
                        ],
=======
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 803a5ae88d8304d7480e74c8935c71b825c3e2b4
                        // 'mimeTypes' => [
                        //     'application/jpeg',
                        //     'application/jpg',
                        // ],
<<<<<<< HEAD
=======
=======
>>>>>>> 5f668aea2ccc9e7fa1880a1e338dfeb4e27236bb
>>>>>>> 803a5ae88d8304d7480e74c8935c71b825c3e2b4
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('dateExpiration')
<<<<<<< HEAD
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'choice_label'=>'id',
                'multiple'=>false,
                'expanded'=>false,])
        ;
=======
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nomCategory',
                'multiple' => false,
                'expanded' => false,
            ]);
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

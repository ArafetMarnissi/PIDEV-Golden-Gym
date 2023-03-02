<?php

namespace App\Form;

use App\Entity\Activite;
<<<<<<< HEAD
=======
use App\Entity\Coach;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomAcitivite',null,array('label' => false))
            ->add('descriptionActivite',null,array('label' => false))
            ->add('dureeActivite',null,array('label' => false))
            ->add('DateActivite',null,array('label' => false))
<<<<<<< HEAD
            ->add('coach',null,array('label' => false))
=======
            ->add('TimeActivite',null,array('label' => false))
            ->add('Coach', EntityType::class, [
                'class'=>Coach::class,
                'choice_label'=>'nomCoach',
                'multiple'=>false,
                'expanded'=>false,
                            ])
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
            ->add('nbrePlace',null,array('label' => false))
            ->add('Image', FileType::class, [

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Vous pouvez mettre que des photos (jpeg/jpg/png/gif)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}

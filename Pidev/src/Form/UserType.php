<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Google\ReCaptcha\V2\ReCaptchaValidator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;




class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email',null,array('label' => false))
        ->add('nom',null,array('label' => false))
        ->add('prenom',null,array('label' => false))
        ->add('password',PasswordType::class,array('label' => false))
        ->add('confirm_password',PasswordType::class,array('label' => false))
        ->add('captcha', CaptchaType::class,[
            'attr' => [
                'label'=>false,
                'class' => "form-control"
            ],
            ]
        )

        ;
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Deputies;
use App\Entity\GovernmentParties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeputiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('middlename', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('surname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('link', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('photo', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('governmentParties', EntityType::class, [
                'class' => GovernmentParties::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deputies::class,
        ]);
    }
}

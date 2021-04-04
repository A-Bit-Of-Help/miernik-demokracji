<?php

namespace App\Form;

use App\Entity\Documents;
use App\Entity\Votes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('path')
            ->add('govPath')
            ->add('votes', EntityType::class, [
                'class' => Votes::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Documents::class,
        ]);
    }
}

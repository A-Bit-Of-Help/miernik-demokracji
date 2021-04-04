<?php

namespace App\Form;

use App\Entity\Timetable;
use App\Entity\Votes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VotesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hour', TimeType::class, [
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('agendaItem', IntegerType::class, [
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('term', EntityType::class, [
                'class' => Timetable::class,
                'choice_label' => function(Timetable $timetable){
                    return $timetable->getDate() . ' - ' . $timetable->getNumber();
                },
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Votes::class,
        ]);
    }
}

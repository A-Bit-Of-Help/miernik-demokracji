<?php

namespace App\Form;

use App\Entity\Deputies;
use App\Entity\Votes;
use App\Entity\VotesResult;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VotesResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('voteResult', TextType::class, [
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('vote', EntityType::class, [
                'class' => Votes::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('deputies', EntityType::class, [
                'class' => Deputies::class,
                'choice_label' => function(Deputies $deputies) {
                    return $deputies->getFirstname() . ' ' . $deputies->getMiddlename() . ' ' . $deputies->getSurname();
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
            'data_class' => VotesResult::class,
        ]);
    }
}

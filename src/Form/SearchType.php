<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => ' ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter a track name or an artist name',
                    'class' => 'form-control w-100 me-3',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Search',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
             ->setMethod('GET');



        ;

    }
}
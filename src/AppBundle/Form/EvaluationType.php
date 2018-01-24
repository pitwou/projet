<?php
// src/AppBundle/Form/EvaluationType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', TextareaType::class, array('label' => 'Commentaire'));
        $builder
                ->add('rate', ChoiceType::class, array(
                    'label' => 'Note',
                    'choices' => array('0' => 0, '1' =>1 , '2' => 2, '3' =>3 , '4' => 4, '5' => 5),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true
                ));

        
    }

}
<?php
// src/Form/MusiqueType.php

namespace App\Form;

use App\Entity\Catalogue\Musique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, NumberType, IntegerType, SubmitType
};
use Symfony\Component\Validator\Constraints as Assert;

class MusiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre ne peut pas être vide.'])
                ]
            ])
            ->add('artiste', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'artiste ne peut pas être vide."])
                ]
            ])
            ->add('prix', NumberType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => "Le prix ne peut pas être nul."]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le prix ne peut pas être négatif.'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                        'message' => 'Le prix ne doit pas avoir plus de 2 chiffres après la virgule.'
                    ])
                ]
            ])
            ->add('disponibilite', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => "La disponibilité ne peut pas être nulle."]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'La disponibilité ne peut pas être négative.'
                    ])
                ]
            ])
            ->add('image', TextType::class) // Ajouter des contraintes si nécessaire
            ->add('dateDeParution', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'JJ/MM/AA'],
                'constraints' => [
                    new Assert\NotBlank(['message' => "La date de parution ne peut pas être vide."]),
                    new Assert\Regex([
                        'pattern' => '/^\d{2}\/\d{2}\/\d{2}$/',
                        'message' => 'La date de parution doit être au format JJ/MM/AA.'
                    ])
                ]
            ])
            ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Musique::class,
        ]);
    }
}

<?php
// src/Form/LivreType.php

namespace App\Form;

use App\Entity\Catalogue\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, TextareaType, NumberType, IntegerType, SubmitType
};
use Symfony\Component\Validator\Constraints as Assert;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        $builder
            ->add('titre', TextType::class, [
                'constraints' => new Assert\NotBlank(['message' => 'Le titre ne peut pas être vide.'])
            ])
            ->add('auteur', TextType::class, [
                'constraints' => new Assert\NotBlank(['message' => "L'auteur ne peut pas être vide."])
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
                    ]),
                ]
            ])
            ->add('genre', TextType::class, [
                'constraints' => new Assert\NotBlank(['message' => 'Le genre ne peut pas être vide.'])
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('disponibilite', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => "La disponibilité ne peut pas être nulle."]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'La disponibilité ne peut pas être négative.'
                    ]),
                ]
            ])
            ->add('image', TextType::class)
            ->add('ISBN', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'ISBN ne peut pas être vide."
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d{3}-\d{1}-\d{2}-\d{6}-\d{1}$/',
                        'message' => "L'ISBN doit être composé de 13 chiffres répartis en 5 segments séparés par un tiret."
                    ]),
                ]
            ])
            ->add('nbPages', IntegerType::class, [
                'constraints' => new Assert\Regex([
                    'pattern' => '/^[1-9][0-9]*$/',
                    'message' => 'Le nombre de pages doit être un entier positif et ne peut pas commencer par 0.'
                ]),
            ])
            ->add('dateDeParution', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'JJ/MM/AA'],
                'constraints' => [
                    new Assert\NotBlank(['message' => "La date de parution ne peut pas être vide."]),
                    new Assert\Regex([
                        'pattern' => '/^\d{2}\/\d{2}\/\d{2}$/',
                        'message' => 'La date de parution doit être au format JJ/MM/AA.'
                    ]),
                ],
            ])
            ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}

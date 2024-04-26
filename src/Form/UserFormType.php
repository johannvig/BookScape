<?php

namespace App\Form;

use App\Entity\User; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('nomCompte', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenomCompte', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('numTelCompte', TelType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false // Si le numéro de téléphone n'est pas obligatoire
            ])
            ->add('adressePostaleCompte', TextType::class, [
                'label' => 'Adresse postale',
                'required' => false
            ])
            ->add('codePostalCompte', TextType::class, [
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('villeCompte', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer les modifications'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Assurez-vous que cela correspond à votre entité utilisateur
        ]);
    }
}

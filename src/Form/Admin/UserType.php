<?php
// src/Form/CommandeType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\{
     ChoiceType, SubmitType
};
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('email', EmailType::class)
			->add('password', PasswordType::class, [
				'constraints' => [new Assert\NotBlank(['message' => "Le mot de passe ne peut pas être vide."])]
			])
			->add('nomCompte', TextType::class)
			->add('prenomCompte', TextType::class)
			->add('numTelCompte', TextType::class, [
				'required' => false,
				'label' => 'Numéro de téléphone',
			])
			->add('adressePostaleCompte', TextType::class, [
				'required' => false,
				'label' => 'Adresse',
			])
			->add('codePostalCompte', TextType::class, [
				'required' => false,
				'label' => 'Code Postal',
			])
			->add('villeCompte', TextType::class, [
				'required' => false,
				'label' => 'Ville',
			])
			->add('paysCompte', TextType::class, [
				'required' => false,
				'label' => 'Pays',
			])
			->add('isVerified', CheckboxType::class, [
				'required' => false,
				'label' => 'Email vérifié'
			])
			->add('roles', ChoiceType::class, [
				'choices' => [
					'Utilisateur' => 'ROLE_USER',
					'Administrateur' => 'ROLE_ADMIN',
				],
				'expanded' => true,
				'multiple' => false,
				'label' => 'Rôle',
				'data' => 'ROLE_USER',
			])
			->add('valider', SubmitType::class);
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }}


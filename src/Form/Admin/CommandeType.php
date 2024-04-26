<?php
// src/Form/CommandeType.php

namespace App\Form;

use App\Entity\Commande\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, ChoiceType, SubmitType
};
use Symfony\Component\Validator\Constraints as Assert;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('statutCommande', ChoiceType::class, [
            'choices' => [
                'En attente' => 'en attente',
                'En cours d\'acheminement' => 'en cours d\'acheminement',
                'Reçu' => 'reçu',
            ],
            'label' => 'Statut de la commande'
        ])
			->add('commandeId', TextType::class, [
				'mapped' => false,
				'data' => $commande->getId(),
				'disabled' => true,
				'label' => 'ID de la commande'
			])
			->add('client', TextType::class, [
				'mapped' => false,
				'data' => $commande->getUser() ? $commande->getUser()->getEmail() : $commande->getEmailVisiteur(),
				'disabled' => $commande->getUser() !== null, // Désactiver si c'est un utilisateur enregistré
				'label' => 'Email du client'
			])
			
			
			->add('nombreArticles', TextType::class, [
				'mapped' => false,
				'data' => count($commande->getLigneCommandes()),
				'disabled' => true,
				'label' => 'Nombre d\'articles'
			])
			->add('articlesDetails', TextareaType::class, [
				'mapped' => false,
				'data' => $articlesDetailsString,
				'disabled' => true,
				'label' => 'Détails des articles',
				'attr' => ['rows' => 5]
			])
			->add('prixTotal', TextType::class, [
				'mapped' => false,
				'data' => $prixTotal,
				'disabled' => true,
				'label' => 'Prix total'
			]);

			if ($commande->getUser()) {
				// Ajout des champs pour les utilisateurs enregistrés
				$formBuilder
					->add('nomCommande', TextType::class, [
						'mapped' => true,
						'data' => $commande->getNomCommande(),
						'disabled' => false,
						'label' => 'Nom'
					])
					->add('prenomCommande', TextType::class, [
						'mapped' => true,
						'data' => $commande->getPrenomCommande(),
						'disabled' => false,
						'label' => 'Prénom'
					]);
			} else {
				// Ajout des champs pour les visiteurs
				$formBuilder
					->add('nomCommande', TextType::class, [
						'mapped' => true,
						'data' => $commande->getNomCommande(), 
						'disabled' => false,
						'label' => 'Nom du visiteur'
					])
					->add('prenomCommande', TextType::class, [
						'mapped' => true,
						'data' => $commande->getPrenomCommande(), 
						'disabled' => false,
						'label' => 'Prénom du visiteur'
					]);
			}

			

			$formBuilder->add('numeroTel', TextType::class, [
				'mapped' => true,
				'data' => $commande->getNumeroTel(),
				'disabled' => false,
				'label' => 'Numéro de Téléphone'
			])
			->add('adresseLivraison', TextType::class, [
				'mapped' => true,
				'data' => $commande->getAdresseLivraison(),
				'disabled' => false,
				'label' => 'Adresse de Livraison'
			])
			->add('villeLivraison', TextType::class, [
				'mapped' => true,
				'data' => $commande->getVilleLivraison(),
				'disabled' => false,
				'label' => 'Ville de Livraison'
			])
			->add('codeLivraison', TextType::class, [
				'mapped' => true,
				'data' => $commande->getCodeLivraison(),
				'disabled' => false,
				'label' => 'Code Postal de Livraison'
			])
			->add('paysLivraison', TextType::class, [
				'mapped' => true,
				'data' => $commande->getPaysLivraison(),
				'disabled' => false,
				'label' => 'Pays de Livraison'
			])
			->add('adresseFacturation', TextType::class, [
				'mapped' => true,
				'data' => $commande->getAdresseFacturation(),
				'disabled' => false,
				'label' => 'Adresse de Facturation'
			])
			->add('villeFacturation', TextType::class, [
				'mapped' => true,
				'data' => $commande->getVilleFacturation(),
				'disabled' => false,
				'label' => 'Ville de Facturation'
			])
			->add('codeFacturation', TextType::class, [
				'mapped' => true,
				'data' => $commande->getCodeFacturation(),
				'disabled' => false,
				'label' => 'Code Postal de Facturation'
			])
			->add('paysFacturation', TextType::class, [
				'mapped' => true,
				'data' => $commande->getPaysFacturation(),
				'disabled' => false,
				'label' => 'Pays de Facturation'
			])
			->add('valider', SubmitType::class);
        
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }}
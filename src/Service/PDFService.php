<?php 

namespace App\Service;

use TCPDF;
use App\Entity\Commande\Commande;

class PDFService {
    public function createCommandePdf(Commande $commande): string {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 10);

        // Construire le contenu du PDF
        $content = '<h1>Détails de la Commande</h1>';
        $content .= '<p><strong>ID de la commande: </strong>' . $commande->getId() . '</p>';
        $content .= '<p><strong>Date de la commande: </strong>' . $commande->getDateCommande()->format('Y-m-d H:i:s') . '</p>';
        $content .= '<p><strong>Email du Visiteur: </strong>' . ($commande->getEmailVisiteur() ?? 'Non disponible') . '</p>';
        $content .= '<p><strong>Statut de la commande: </strong>' . $commande->getStatutCommande() . '</p>';

        // Ajouter les informations de livraison et facturation
        $content .= '<h2>Informations de Livraison</h2>';
        $content .= '<p>Adresse: ' . $commande->getAdresseLivraison() . ', ' . $commande->getVilleLivraison() . ', ' . $commande->getCodeLivraison() . ', ' . $commande->getPaysLivraison() . '</p>';

        $content .= '<h2>Informations de Facturation</h2>';
        $content .= '<p>Adresse: ' . $commande->getAdresseFacturation() . ', ' . $commande->getVilleFacturation() . ', ' . $commande->getCodeFacturation() . ', ' . $commande->getPaysFacturation() . '</p>';

        // Ajouter les détails des articles commandés
        $content .= '<h2>Articles Commandés</h2>';
        foreach ($commande->getLigneCommandes() as $ligne) {
            $article = $ligne->getArticle();
            $content .= '<p>Produit: ' . $article->getTitre() . ' | Quantité: ' . $ligne->getQuantite() . ' | Prix: ' . $ligne->getPrix() . '</p>';
        }

        $pdf->writeHTML($content, true, false, true, false, '');

        return $pdf->Output('', 'S'); // Retourne le contenu du PDF
    }
}

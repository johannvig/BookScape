<?php

namespace App\Entity\Panier;

use ArrayObject;
use App\Entity\Catalogue\Article;

class Panier
{
	private array $idsArticles;
    private float $total;

    private ArrayObject $lignesPanier;

	public function __construct()
    {
		$this->lignesPanier = new ArrayObject();
		$this->idsArticles = [];
    }

	public function setTotal(): void
	{
		$this->recalculer();
    }
	
	public function getTotal(): ?float
	{
		$this->recalculer();
		return $this->total;
    }
	
	public function getLignesPanier(): ?ArrayObject
	{
		return $this->lignesPanier;
	}
	
	public function recalculer(): void
	{
		$it = $this->getLignesPanier()->getIterator();
		$this->total = 0.0 ;
		while ($it->valid()) {
			$ligne = $it->current();
			$ligne->recalculer() ;
			$this->total += $ligne->getPrixTotal() ;
			$it->next();
		}
	}
	


	public function ajouterLigne(Article $article, int $quantite): void {
        // Chercher si une ligne pour cet article existe déjà dans le panier
        $lignePanierExiste = $this->chercherLignePanier($article);

        if ($lignePanierExiste === null) {
            // Créer une nouvelle ligne si elle n'existe pas
            $nouvelleLigne = new LignePanier();
            $nouvelleLigne->setArticle($article);
            $nouvelleLigne->setQuantite($quantite);
            $nouvelleLigne->recalculer(); // Calculer le prix total pour cette ligne
            $this->lignesPanier->append($nouvelleLigne); // Ajouter la nouvelle ligne au panier
        } else {
            // Si la ligne existe déjà, mettre à jour la quantité
            $lignePanierExiste->setQuantite($lignePanierExiste->getQuantite() + $quantite);
            $lignePanierExiste->recalculer(); // Recalculer le prix total pour cette ligne
        }

        // Recalculer le total du panier après avoir ajouté ou mis à jour la ligne
        $this->recalculer();
    }
	
	public function chercherLignePanier(Article $article): ?LignePanier
	{
		$lignePanier = null ;
		$it = $this->getLignesPanier()->getIterator();
		while ($it->valid()) {
			$ligne = $it->current();
			if ($ligne->getArticle()->getId() == $article->getId())
				$lignePanier = $ligne ;
			$it->next();
		}
		return $lignePanier ;
	}
	
	public function supprimerLigne(int $id): void
	{
		$existe = false ;
		$it = $this->getLignesPanier()->getIterator();
		while ($it->valid()) {
			$ligne = $it->current();
			if ($ligne->getArticle()->getId() == $id) {
				$existe = true ;
				$key = $it->key();
			}
			$it->next();
		}
		if ($existe) {
			$this->getLignesPanier()->offsetUnset($key);
		}
		$idKey = array_search($id, $this->idsArticles);
		if ($idKey !== false) {
			unset($this->idsArticles[$idKey]);
		}

	
		$this->idsArticles = array_values($this->idsArticles);
	}

	public function chercherLignePanierParId(int $articleId): ?LignePanier
{
    foreach ($this->getLignesPanier() as $ligne) {
        if ($ligne->getArticle()->getId() == $articleId) {
            return $ligne;
        }
    }

    return null;
}



}





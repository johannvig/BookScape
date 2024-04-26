<?php

namespace App\Entity\Catalogue;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "article_type", type: "string")]
#[ORM\DiscriminatorMap(["article" => "Article", "livre" => "Livre", "musique" => "Musique"])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'titre')]
    private ?string $titre = null;

    #[ORM\Column(name: 'prix')]
    private ?float $prix = null;

    #[ORM\Column(name: 'disponibilite')]
    private ?int $disponibilite = null;
	
    #[ORM\Column(length: 255, name: 'image')]
    private ?string $image = null;


    #[ORM\ManyToMany(targetEntity: "App\Entity\Commande\Commande", mappedBy: "articles")]
    private Collection $commandes;


    #[ORM\OneToMany(mappedBy: "article", targetEntity: "App\Entity\Avis")]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: "App\Entity\NotificationsStock")]
    private Collection $notificationsStock;

    // Modify the constructor to initialize the 'avis' property
    public function __construct() {
        $this->commandes = new ArrayCollection();
        $this->avis = new ArrayCollection(); // Initialize the Collection of Avis
    }


    public function getId(): ?int
    {
        return $this->id;
    }
	
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDisponibilite(): ?int
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(int $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setArticle($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->avis->removeElement($avis)) {
            // set the owning side to null (unless already changed)
            if ($avis->getArticle() === $this) {
                $avis->setArticle(null);
            }
        }

        return $this;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->addArticle($this); // This assumes Commande has addArticle method
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            $commande->removeArticle($this); // This assumes Commande has removeArticle method
        }

        return $this;
    }


    public function getNotificationsStock(): Collection
    {
        return $this->notificationsStock;
    }

    public function addNotificationsStock(NotificationsStock $notificationsStock): self
    {
        if (!$this->notificationsStock->contains($notificationsStock)) {
            $this->notificationsStock[] = $notificationsStock;
            $notificationsStock->setArticle($this);
        }

        return $this;
    }

    public function removeNotificationsStock(NotificationsStock $notificationsStock): self
    {
        if ($this->notificationsStock->removeElement($notificationsStock)) {
            // set the owning side to null (unless already changed)
            if ($notificationsStock->getArticle() === $this) {
                $notificationsStock->setArticle(null);
            }
        }

        return $this;
    }

}


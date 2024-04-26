<?php

namespace App\Entity\Commande;
use App\Entity\User;
use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date_commande = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $emailVisiteur = null;

    #[ORM\Column(length: 20)]
    private ?string $Statut_commande = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $codeLivraison = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $villeLivraison = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $paysLivraison = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $adresseFacturation = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $villeFacturation = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $codeFacturation = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $paysFacturation = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $numeroTel = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $nomCommande = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $prenomCommande = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;


    #[ORM\ManyToMany(targetEntity: "App\Entity\Catalogue\Article", inversedBy: "commandes")]
    #[ORM\JoinTable(name: "commande_article")]
    private Collection $articles;

    #[ORM\OneToOne(targetEntity: CommandePdf::class, mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?CommandePdf $commandePdf = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist'], orphanRemoval: true)]
    private $ligneCommandes;



    public function __construct() {
        $this->articles = new ArrayCollection();
        $this->ligneCommandes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }



    public function setIdCommande(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->Date_commande;
    }

    public function setDateCommande(\DateTimeInterface $Date_commande): static
    {
        $this->Date_commande = $Date_commande;

        return $this;
    }

    public function getStatutCommande(): ?string
    {
        return $this->Statut_commande;
    }

    public function setStatutCommande(string $Statut_commande): static
    {
        $this->Statut_commande = $Statut_commande;

        return $this;
    }


    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addCommande($this); // Make sure you have this method in your Article entity
        }

        return $this;
    }

    

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeCommande($this); // Make sure you have this method in your Article entity
        }

        return $this;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the user associated with the commande.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }


    public function getEmailVisiteur(): ?string
    {
        return $this->emailVisiteur;
    }

    public function setEmailVisiteur(?string $emailVisiteur): self
    {
        $this->emailVisiteur = $emailVisiteur;
        return $this;
    }

    public function getAdresseLivraison(): ?string {
        return $this->adresseLivraison;
    }
    
    public function setAdresseLivraison(?string $adresseLivraison): self {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }
    
    public function getCodeLivraison(): ?string {
        return $this->codeLivraison;
    }
    
    public function setCodeLivraison(?string $codeLivraison): self {
        $this->codeLivraison = $codeLivraison;
        return $this;
    }
    
    public function getVilleLivraison(): ?string {
        return $this->villeLivraison;
    }
    
    public function setVilleLivraison(?string $villeLivraison): self {
        $this->villeLivraison = $villeLivraison;
        return $this;
    }
    
    public function getPaysLivraison(): ?string {
        return $this->paysLivraison;
    }
    
    public function setPaysLivraison(?string $paysLivraison): self {
        $this->paysLivraison = $paysLivraison;
        return $this;
    }
    
    public function getAdresseFacturation(): ?string {
        return $this->adresseFacturation;
    }
    
    public function setAdresseFacturation(?string $adresseFacturation): self {
        $this->adresseFacturation = $adresseFacturation;
        return $this;
    }
    
    public function getVilleFacturation(): ?string {
        return $this->villeFacturation;
    }
    
    public function setVilleFacturation(?string $villeFacturation): self {
        $this->villeFacturation = $villeFacturation;
        return $this;
    }
    
    public function getCodeFacturation(): ?string {
        return $this->codeFacturation;
    }
    
    public function setCodeFacturation(?string $codeFacturation): self {
        $this->codeFacturation = $codeFacturation;
        return $this;
    }
    
    public function getPaysFacturation(): ?string {
        return $this->paysFacturation;
    }
    
    public function setPaysFacturation(?string $paysFacturation): self {
        $this->paysFacturation = $paysFacturation;
        return $this;
    }
    
    public function getNumeroTel(): ?string {
        return $this->numeroTel;
    }
    
    public function setNumeroTel(?string $numeroTel): self {
        $this->numeroTel = $numeroTel;
        return $this;
    }
    
    public function getNomCommande(): ?string {
        return $this->nomCommande;
    }
    
    public function setNomCommande(?string $nomCommande): self {
        $this->nomCommande = $nomCommande;
        return $this;
    }
    
    public function getPrenomCommande(): ?string {
        return $this->prenomCommande;
    }
    
    public function setPrenomCommande(?string $prenomCommande): self {
        $this->prenomCommande = $prenomCommande;
        return $this;
    }

    public function getCommandePdf(): ?CommandePdf
    {
        return $this->commandePdf;
    }

    public function setCommandePdf(CommandePdf $commandePdf): self
    {
        // set the owning side of the relation if necessary
        if ($commandePdf->getCommande() !== $this) {
            $commandePdf->setCommande($this);
        }

        $this->commandePdf = $commandePdf;

        return $this;
    }
}

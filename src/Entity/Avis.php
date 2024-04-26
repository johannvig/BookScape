<?php

namespace App\Entity;

use App\Entity\Catalogue\Article;
use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $Note_avis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Commentaire_avis = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Date_avis = null;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Catalogue\Article", inversedBy: "avis")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function setIdAvis(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNoteAvis(): ?int
    {
        return $this->Note_avis;
    }

    public function setNoteAvis(int $Note_avis): static
    {
        $this->Note_avis = $Note_avis;

        return $this;
    }

    public function getCommentaireAvis(): ?string
    {
        return $this->Commentaire_avis;
    }

    public function setCommentaireAvis(?string $Commentaire_avis): static
    {
        $this->Commentaire_avis = $Commentaire_avis;

        return $this;
    }

    public function getDateAvis(): ?\DateTimeInterface
    {
        return $this->Date_avis;
    }

    public function setDateAvis(\DateTimeInterface $Date_avis): static
    {
        $this->Date_avis = $Date_avis;

        return $this;
    }

    

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;
        return $this;
    }


    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the user associated with the avis.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}

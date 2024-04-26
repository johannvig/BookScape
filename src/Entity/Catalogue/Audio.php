<?php

namespace App\Catalogue\Entity;

use App\Entity\Catalogue\Article;
use App\Repository\AudioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AudioRepository::class)]
class Audio extends Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $statut_ecoute = null;

    #[ORM\Column(length: 255)]
    private ?string $nomMusique = null;

    #[ORM\Column]
    private ?float $tempsEcoute = null; 

    #[ORM\Column(length: 255, name: 'image')]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isStatutEcoute(): ?bool
    {
        return $this->statut_ecoute;
    }

    public function setStatutEcoute(bool $statut_ecoute): static
    {
        $this->statut_ecoute = $statut_ecoute;

        return $this;
    }

    public function getNomMusique(): ?string
    {
        return $this->nomMusique;
    }

    public function setNomMusique(string $nomMusique): static
    {
        $this->nomMusique = $nomMusique;

        return $this;
    }

    public function getTempsEcoute(): ?float
    {
        return $this->tempsEcoute;
    }

    public function setTempsEcoute(float $tempsEcoute): static
    {
        $this->tempsEcoute = $tempsEcoute;
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
}
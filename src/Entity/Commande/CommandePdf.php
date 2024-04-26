<?php

namespace App\Entity\Commande;

use App\Entity;


use App\Repository\CommandePdfRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandePdfRepository::class)]
class CommandePdf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'blob')]
    private $pdfContent;

    

    #[ORM\OneToOne(targetEntity: "App\Entity\Commande\Commande", cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $commande;


    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPdfContent()
    {
        return $this->pdfContent;
    }

    public function setPdfContent($pdfContent): self
    {
        $this->pdfContent = $pdfContent;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}




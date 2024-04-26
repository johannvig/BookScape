<?php

namespace App\Entity;

use App\Repository\NotificationsStockRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Catalogue\Article;

#[ORM\Entity(repositoryClass: NotificationsStockRepository::class)]
class NotificationsStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180)]
    private ?string $email = null;


    #[ORM\ManyToOne(targetEntity: Article::class)] 
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
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
}

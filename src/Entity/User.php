<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenomCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numTelCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adressePostaleCompte = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $codePostalCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $villeCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paysCompte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activation_token =null;

    #[ORM\Column]
    private bool $isAdmin = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $resetToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenTimestamp = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $activationTokenTimestamp = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: "App\Entity\Commande\Commande", cascade: ['persist'])]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Avis::class, cascade: ['persist'])]
    private Collection $avis;


    public function __construct() {
        $this->commandes = new ArrayCollection();
        $this->avis = new ArrayCollection();
   
    }

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNomCompte(): ?string
    {
        return $this->nomCompte;
    }

    public function setNomCompte(?string $nomCompte): self
    {
        $this->nomCompte = $nomCompte;
        return $this;
    }

    public function getPrenomCompte(): ?string
    {
        return $this->prenomCompte;
    }

    public function setPrenomCompte(?string $prenomCompte): self
    {
        $this->prenomCompte = $prenomCompte;
        return $this;
    }

    public function getNumTelCompte(): ?string
    {
        return $this->numTelCompte;
    }

    public function setNumTelCompte(?string $numTelCompte): self
    {
        $this->numTelCompte = $numTelCompte;
        return $this;
    }

    public function getAdressePostaleCompte(): ?string
    {
        return $this->adressePostaleCompte;
    }

    public function setAdressePostaleCompte(?string $adressePostaleCompte): self
    {
        $this->adressePostaleCompte = $adressePostaleCompte;
        return $this;
    }

    public function getCodePostalCompte(): ?int
    {
        return $this->codePostalCompte;
    }

    public function setCodePostalCompte(?int $codePostalCompte): self
    {
        $this->codePostalCompte = $codePostalCompte;
        return $this;
    }

    public function getVilleCompte(): ?string
    {
        return $this->villeCompte;
    }

    public function setVilleCompte(?string $villeCompte): self
    {
        $this->villeCompte = $villeCompte;
        return $this;
    }

    public function getPaysCompte(): ?string
    {
        return $this->paysCompte;
    }

    public function setPaysCompte(?string $paysCompte): self
    {
        $this->paysCompte = $paysCompte;
        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;
        return $this;
    }

    public function getResetTokenTimestamp(): ?\DateTimeInterface
    {
        return $this->resetTokenTimestamp;
    }

    public function setResetTokenTimestamp(?\DateTimeInterface $resetTokenTimestamp): self
    {
        $this->resetTokenTimestamp = $resetTokenTimestamp;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getActivationTokenTimestamp(): ?\DateTimeInterface
    {
        return $this->activationTokenTimestamp;
    }

    public function setActivationTokenTimestamp(?\DateTimeInterface $activationTokenTimestamp): self
    {
        $this->activationTokenTimestamp = $activationTokenTimestamp;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Automatically add ROLE_USER to ensure every user has at least one role
        $roles[] = 'ROLE_USER';
        
        // Conditionally add ROLE_ADMIN if the user is flagged as an admin
        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    public function setRoles($roles): self
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        $this->roles = $roles;

        return $this;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUser($this); // Lie la commande à cet utilisateur
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setUser($this); // Lie l'avis à cet utilisateur
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->avis->removeElement($avis)) {
            // set the owning side to null (unless already changed)
            if ($avis->getUser() === $this) {
                $avis->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }





}
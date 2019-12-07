<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StocksRepository")
 */
class Stocks
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produits", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $projet;

    /**
     * @ORM\Column(type="float")
     */
    private $quantite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mouvements", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mouvement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Clients", inversedBy="stocks")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unites", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PieceJointe", inversedBy="stocks")
     */
    private $piece;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Validations", inversedBy="stocks")
     */
    private $validation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="stocks")
     */
    private $validateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stocks", inversedBy="stocks")
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stocks", mappedBy="stock")
     */
    private $stocks;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $causeAnnulation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSaisie;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateValidation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etats", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $referencePanier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="stocksSource")
     */
    private $AutreSource;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Site;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Remarque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produits
    {
        return $this->produit;
    }

    public function setProduit(?Produits $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMouvement(): ?Mouvements
    {
        return $this->mouvement;
    }

    public function setMouvement(?Mouvements $mouvement): self
    {
        $this->mouvement = $mouvement;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getUnite(): ?Unites
    {
        return $this->unite;
    }

    public function setUnite(?Unites $unite): self
    {
        $this->unite = $unite;

        return $this;
    }

    public function getOperateur(): ?User
    {
        return $this->operateur;
    }

    public function setOperateur(?User $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getPiece(): ?PieceJointe
    {
        return $this->piece;
    }

    public function setPiece(?PieceJointe $piece): self
    {
        $this->piece = $piece;

        return $this;
    }

    public function getValidation(): ?Validations
    {
        return $this->validation;
    }

    public function setValidation(?Validations $validation): self
    {
        $this->validation = $validation;

        return $this;
    }

    public function getValidateur(): ?User
    {
        return $this->validateur;
    }

    public function setValidateur(?User $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getStock(): ?self
    {
        return $this->stock;
    }

    public function setStock(?self $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(self $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setStock($this);
        }

        return $this;
    }

    public function removeStock(self $stock): self
    {
        if ($this->stocks->contains($stock)) {
            $this->stocks->removeElement($stock);
            // set the owning side to null (unless already changed)
            if ($stock->getStock() === $this) {
                $stock->setStock(null);
            }
        }

        return $this;
    }

    public function getCauseAnnulation(): ?string
    {
        return $this->causeAnnulation;
    }

    public function setCauseAnnulation(?string $causeAnnulation): self
    {
        $this->causeAnnulation = $causeAnnulation;

        return $this;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): self
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getEtat(): ?Etats
    {
        return $this->etat;
    }

    public function setEtat(?Etats $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getReferencePanier(): ?string
    {
        return $this->referencePanier;
    }

    public function setReferencePanier(string $referencePanier): self
    {
        $this->referencePanier = $referencePanier;

        return $this;
    }

    public function getAutreSource(): ?Projet
    {
        return $this->AutreSource;
    }

    public function setAutreSource(?Projet $AutreSource): self
    {
        $this->AutreSource = $AutreSource;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->Site;
    }

    public function setSite(?string $Site): self
    {
        $this->Site = $Site;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->Remarque;
    }

    public function setRemarque(?string $Remarque): self
    {
        $this->Remarque = $Remarque;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}

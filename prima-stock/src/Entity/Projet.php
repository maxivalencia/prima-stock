<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Clients", inversedBy="projets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stocks", mappedBy="projet")
     */
    private $stocks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stocks", mappedBy="AutreSource")
     */
    private $stocksSource;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Lieu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Site;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->stocksSource = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return strtoupper($this->nom);
    }

    public function setNom(string $nom): self
    {
        $this->nom = strtoupper($nom);

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

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Stocks[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stocks $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setProjet($this);
        }

        return $this;
    }

    public function removeStock(Stocks $stock): self
    {
        if ($this->stocks->contains($stock)) {
            $this->stocks->removeElement($stock);
            // set the owning side to null (unless already changed)
            if ($stock->getProjet() === $this) {
                $stock->setProjet(null);
            }
        }

        return $this;
    }

    /**
    * toString
    * @return string
    */
    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * @return Collection|Stocks[]
     */
    public function getStocksSource(): Collection
    {
        return $this->stocksSource;
    }

    public function addStocksSource(Stocks $stocksSource): self
    {
        if (!$this->stocksSource->contains($stocksSource)) {
            $this->stocksSource[] = $stocksSource;
            $stocksSource->setAutreSource($this);
        }

        return $this;
    }

    public function removeStocksSource(Stocks $stocksSource): self
    {
        if ($this->stocksSource->contains($stocksSource)) {
            $this->stocksSource->removeElement($stocksSource);
            // set the owning side to null (unless already changed)
            if ($stocksSource->getAutreSource() === $this) {
                $stocksSource->setAutreSource(null);
            }
        }

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->Lieu;
    }

    public function setLieu(?string $Lieu): self
    {
        $this->Lieu = $Lieu;

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

}

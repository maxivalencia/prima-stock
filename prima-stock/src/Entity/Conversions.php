<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversionsRepository")
 */
class Conversions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unites", inversedBy="conversions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unitesource;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unites", inversedBy="conversions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unitesdestinataire;

    /**
     * @ORM\Column(type="float")
     */
    private $valeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitesource(): ?Unites
    {
        return $this->unitesource;
    }

    public function setUnitesource(?Unites $unitesource): self
    {
        $this->unitesource = $unitesource;

        return $this;
    }

    public function getUnitesdestinataire(): ?Unites
    {
        return $this->unitesdestinataire;
    }

    public function setUnitesdestinataire(?Unites $unitesdestinataire): self
    {
        $this->unitesdestinataire = $unitesdestinataire;

        return $this;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }
}

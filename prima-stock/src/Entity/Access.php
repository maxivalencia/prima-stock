<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessRepository")
 */
class Access
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
    private $access;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Autorisations", mappedBy="access")
     */
    private $autorisations;

    public function __construct()
    {
        $this->autorisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccess(): ?string
    {
        return strtoupper($this->access);
    }

    public function setAccess(string $access): self
    {
        $this->access = strtoupper($access);

        return $this;
    }

    /**
     * @return Collection|Autorisations[]
     */
    public function getAutorisations(): Collection
    {
        return $this->autorisations;
    }

    public function addAutorisation(Autorisations $autorisation): self
    {
        if (!$this->autorisations->contains($autorisation)) {
            $this->autorisations[] = $autorisation;
            $autorisation->setAccess($this);
        }

        return $this;
    }

    public function removeAutorisation(Autorisations $autorisation): self
    {
        if ($this->autorisations->contains($autorisation)) {
            $this->autorisations->removeElement($autorisation);
            // set the owning side to null (unless already changed)
            if ($autorisation->getAccess() === $this) {
                $autorisation->setAccess(null);
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
        return $this->getAccess();
    }

}

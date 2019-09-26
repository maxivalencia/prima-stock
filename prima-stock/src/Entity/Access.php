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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Username", mappedBy="Access")
     */
    private $usernames;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="access")
     */
    private $users;

    public function __construct()
    {
        $this->autorisations = new ArrayCollection();
        $this->usernames = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection|Username[]
     */
    public function getUsernames(): Collection
    {
        return $this->usernames;
    }

    public function addUsername(Username $username): self
    {
        if (!$this->usernames->contains($username)) {
            $this->usernames[] = $username;
            $username->setAccess($this);
        }

        return $this;
    }

    public function removeUsername(Username $username): self
    {
        if ($this->usernames->contains($username)) {
            $this->usernames->removeElement($username);
            // set the owning side to null (unless already changed)
            if ($username->getAccess() === $this) {
                $username->setAccess(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAccess($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getAccess() === $this) {
                $user->setAccess(null);
            }
        }

        return $this;
    }

}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DeputiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=DeputiesRepository::class)
 */
class Deputies
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $middlename;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $surname;

    /**
     * @ORM\ManyToOne(targetEntity=GovernmentParties::class, inversedBy="deputies")
     */
    private $governmentParties;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity=VotesResult::class, mappedBy="deputies")
     */
    private $votesResults;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $details;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct()
    {
        $this->votesResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getMiddlename(): ?string
    {
        return $this->middlename;
    }

    public function setMiddlename(?string $middlename): self
    {
        $this->middlename = $middlename;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getGovernmentParties(): ?GovernmentParties
    {
        return $this->governmentParties;
    }

    public function setGovernmentParties(?GovernmentParties $governmentParties): self
    {
        $this->governmentParties = $governmentParties;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|VotesResult[]
     */
    public function getVotesResults(): Collection
    {
        return $this->votesResults;
    }

    public function addVotesResult(VotesResult $votesResult): self
    {
        if (!$this->votesResults->contains($votesResult)) {
            $this->votesResults[] = $votesResult;
            $votesResult->setDeputies($this);
        }

        return $this;
    }

    public function removeVotesResult(VotesResult $votesResult): self
    {
        if ($this->votesResults->removeElement($votesResult)) {
            // set the owning side to null (unless already changed)
            if ($votesResult->getDeputies() === $this) {
                $votesResult->setDeputies(null);
            }
        }

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}

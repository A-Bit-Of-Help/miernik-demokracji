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
    private $secondname;

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
     * @ORM\Column(type="blob", nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity=VotesResult::class, mappedBy="deputies")
     */
    private $votesResults;

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

    public function getSecondname(): ?string
    {
        return $this->secondname;
    }

    public function setSecondname(?string $secondname): self
    {
        $this->secondname = $secondname;

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
}

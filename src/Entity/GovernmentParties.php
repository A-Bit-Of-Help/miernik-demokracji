<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GovernmentPartiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=GovernmentPartiesRepository::class)
 */
class GovernmentParties
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity=Deputies::class, mappedBy="governmentParties")
     */
    private $deputies;

    public function __construct()
    {
        $this->deputies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection|Deputies[]
     */
    public function getDeputies(): Collection
    {
        return $this->deputies;
    }

    public function addDeputy(Deputies $deputy): self
    {
        if (!$this->deputies->contains($deputy)) {
            $this->deputies[] = $deputy;
            $deputy->setGovernmentParties($this);
        }

        return $this;
    }

    public function removeDeputy(Deputies $deputy): self
    {
        if ($this->deputies->removeElement($deputy)) {
            // set the owning side to null (unless already changed)
            if ($deputy->getGovernmentParties() === $this) {
                $deputy->setGovernmentParties(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TimetableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=TimetableRepository::class)
 */
class Timetable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $startTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="integer")
     */
    private $term;

    /**
     * @ORM\OneToMany(targetEntity=Votes::class, mappedBy="term")
     */
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

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

    public function getTerm(): ?int
    {
        return $this->term;
    }

    public function setTerm(int $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return Collection|Votes[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Votes $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setTerm($this);
        }

        return $this;
    }

    public function removeVote(Votes $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getTerm() === $this) {
                $vote->setTerm(null);
            }
        }

        return $this;
    }
}

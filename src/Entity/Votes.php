<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VotesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=VotesRepository::class)
 */
class Votes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $hour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $agendaItem;

    /**
     * @ORM\ManyToOne(targetEntity=Timetable::class, inversedBy="votes")
     */
    private $term;

    /**
     * @ORM\OneToMany(targetEntity=Documents::class, mappedBy="votes")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=VotesResult::class, mappedBy="vote")
     */
    private $votesResults;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->votesResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHour(): ?\DateTimeInterface
    {
        return $this->hour;
    }

    public function setHour(\DateTimeInterface $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAgendaItem(): ?int
    {
        return $this->agendaItem;
    }

    public function setAgendaItem(int $agendaItem): self
    {
        $this->agendaItem = $agendaItem;

        return $this;
    }

    public function getTerm(): ?Timetable
    {
        return $this->term;
    }

    public function setTerm(?Timetable $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setVotes($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getVotes() === $this) {
                $document->setVotes(null);
            }
        }

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
            $votesResult->setVote($this);
        }

        return $this;
    }

    public function removeVotesResult(VotesResult $votesResult): self
    {
        if ($this->votesResults->removeElement($votesResult)) {
            // set the owning side to null (unless already changed)
            if ($votesResult->getVote() === $this) {
                $votesResult->setVote(null);
            }
        }

        return $this;
    }
}

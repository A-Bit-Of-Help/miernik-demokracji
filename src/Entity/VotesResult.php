<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VotesResultRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=VotesResultRepository::class)
 */
class VotesResult
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Votes::class, inversedBy="votesResults")
     */
    private $vote;

    /**
     * @ORM\ManyToOne(targetEntity=Deputies::class, inversedBy="votesResults")
     */
    private $deputies;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $voteResult;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVote(): ?Votes
    {
        return $this->vote;
    }

    public function setVote(?Votes $vote): self
    {
        $this->vote = $vote;

        return $this;
    }

    public function getDeputies(): ?Deputies
    {
        return $this->deputies;
    }

    public function setDeputies(?Deputies $deputies): self
    {
        $this->deputies = $deputies;

        return $this;
    }

    public function getVoteResult(): ?string
    {
        return $this->voteResult;
    }

    public function setVoteResult(string $voteResult): self
    {
        $this->voteResult = $voteResult;

        return $this;
    }
}

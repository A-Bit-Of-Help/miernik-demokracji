<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentsRepository::class)
 */
class Documents
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
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $govPath;

    /**
     * @ORM\ManyToOne(targetEntity=Votes::class, inversedBy="documents")
     */
    private $votes;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getGovPath(): ?string
    {
        return $this->govPath;
    }

    public function setGovPath(string $govPath): self
    {
        $this->govPath = $govPath;

        return $this;
    }

    public function getVotes(): ?Votes
    {
        return $this->votes;
    }

    public function setVotes(?Votes $votes): self
    {
        $this->votes = $votes;

        return $this;
    }
}

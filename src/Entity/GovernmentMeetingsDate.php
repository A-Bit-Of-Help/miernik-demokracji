<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GovernmentMeetingsDateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=GovernmentMeetingsDateRepository::class)
 */
class GovernmentMeetingsDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Timetable::class, inversedBy="yes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $GovernmentMeeting;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGovernmentMeeting(): ?Timetable
    {
        return $this->GovernmentMeeting;
    }

    public function setGovernmentMeeting(?Timetable $GovernmentMeeting): self
    {
        $this->GovernmentMeeting = $GovernmentMeeting;

        return $this;
    }
}

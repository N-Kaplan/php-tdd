<?php

namespace App\Entity;

use App\Repository\BookingsRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingsRepository::class)]
class Bookings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Room::class, cascade: ["persist"], inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $roomId;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $userId;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'datetime')]
    private $endDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getroomId(): ?Room
    {
        return $this->roomId;
    }

    public function setroomId(?Room $roomId): self
    {
        $this->roomId = $roomId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
//    TODO: 4 is a magical number, refactor!
    function canBookTime(DateTime $startDate, DateTime $endDate): bool {

        $timeDifference = $startDate->diff($endDate)->h;
        return $timeDifference <= 4 && $startDate < $endDate;
    }
}

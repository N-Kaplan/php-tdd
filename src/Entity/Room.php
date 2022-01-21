<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'boolean')]
    private bool $onlyForPremiumMembers;

    #[ORM\OneToMany(mappedBy: 'roomId', targetEntity: Bookings::class)]
    private Collection $bookings;

    public function __construct( bool $premium)
    {
        $this->bookings = new ArrayCollection();
        $this->onlyForPremiumMembers = $premium;
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

    public function getOnlyForPremiumMembers(): ?bool
    {
        return $this->onlyForPremiumMembers;
    }

    public function setOnlyForPremiumMembers(bool $onlyForPremiumMembers): self
    {
        $this->onlyForPremiumMembers = $onlyForPremiumMembers;

        return $this;
    }

    /**
     * @return Collection|Bookings[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBookings(Bookings $bookings): self
    {
        if (!$this->bookings->contains($bookings)) {
            $this->bookings[] = $bookings;
            $bookings->setRoomId($this);
        }

        return $this;
    }

    public function removeBookings(Bookings $bookings): self
    {
        if ($this->bookings->removeElement($bookings)) {
            // set the owning side to null (unless already changed)
            if ($bookings->getRoomId() === $this) {
                $bookings->setRoomId(null);
            }
        }

        return $this;
    }

    //business logic

    //initial version of the function, to pass the test:
    // return true;
    function canBook(User $user): bool {

        return ($this->getOnlyForPremiumMembers() && $user->getPremiumMember()) || !$this->getOnlyForPremiumMembers();
    }

    //    TODO: 4 is a magical number, refactor!
    function canBookTime(DateTime $startDate, DateTime $endDate): bool {

//        $timeDifference = $startDate->diff($endDate)->h; only returns the difference in hours, ignores days, minutes, etc.
//        total time difference in minutes
        $timeDifferenceMinutes = (($endDate)->getTimestamp() - ($startDate)->getTimestamp()) / 60;
        return $timeDifferenceMinutes <= 4*60 && $startDate < $endDate;
    }

    //get reservations from database
    public function getReservations(ManagerRegistry $doctrine): array
    {
        $room = $doctrine->getManager()->getRepository(Room::class)->find($this->getId());
        $bookings = $room->getBookings();
        $reservations = [];
        foreach ($bookings as &$value) {
            $reservations[] = ['startTime' => $value->getStartDate(), 'endTime' => $value->getEndDate()];
        }
        //array within array?
        return $reservations[0];
    }

    public function isFree(DateTime $start, DateTime $end, array $reservations): bool
    {
        // $bookings = $this->getBookings();
        $free = true;
        foreach ($reservations as &$value) {
            if ($start <= $value['startTime'] && $end >= $value['startTime']) {
                $free = false;
            } elseif ($start > $value['startTime'] && $start < $value['endTime']) {
                $free = false;
            }
        }
        return $free;
    }

    //necessary for form
    public function __toString(){
        return strval($this->getId());
    }
}

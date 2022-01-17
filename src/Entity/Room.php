<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\OneToMany(mappedBy: 'roomID', targetEntity: Bookings::class)]
    private ArrayCollection $bookings;

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

    //initial version of the function, to pass the test:
    // return true;
    function canBook(User $user): bool {

        return ($this->getOnlyForPremiumMembers() && $user->getPremiumMember()) || !$this->getOnlyForPremiumMembers();
    }
}

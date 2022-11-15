<?php

namespace App\Entity;

use App\Repository\ChatRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRoomRepository::class)
 */
class ChatRoom
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $socketId;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="chatRooms")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="chatRoom")
     */
    private $chat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $socketId2;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->chat = new ArrayCollection();
        $this->created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getSocketId(): ?int
    {
        return $this->socketId;
    }

    public function setSocketId(int $socketId): self
    {
        $this->socketId = $socketId;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getChat(): Collection
    {
        return $this->chat;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chat->contains($chat)) {
            $this->chat[] = $chat;
            $chat->setChatRoom($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chat->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getChatRoom() === $this) {
                $chat->setChatRoom(null);
            }
        }

        return $this;
    }

    public function getSocketId2(): ?int
    {
        return $this->socketId2;
    }

    public function setSocketId2(int $socketId2): self
    {
        $this->socketId2 = $socketId2;

        return $this;
    }
}

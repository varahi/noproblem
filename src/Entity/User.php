<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @method string getUserIdentifier()
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVerified;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="user")
     */
    private $ticket;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="user")
     */
    private $answers;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $experience;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $education;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="users")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="users")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Tariff::class, inversedBy="users")
     */
    private $tariff;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="client", cascade={"persist"}, orphanRemoval=true)
     */
    private $jobs;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="owner")
     */
    private $ownersJobs;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastlogin;

    /**
     * @ORM\OneToMany(targetEntity=Worksheet::class, mappedBy="user")
     */
    private $worksheets;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $emailVerified;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $phoneVerified;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $passportVerified;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $preferredContactMethod;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\ManyToMany(targetEntity=Worksheet::class, inversedBy="featuredUsers")
     */
    private $featuredProfiles;

    /**
     * @ORM\ManyToMany(targetEntity=Job::class, inversedBy="featuredUsers")
     */
    private $featuredJobs;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $inactive;

    /**
     * @ORM\ManyToOne(targetEntity=Citizen::class, inversedBy="users")
     */
    private $citizen;

    /**
     * @ORM\ManyToMany(targetEntity=ChatRoom::class, mappedBy="users")
     */
    private $chatRooms;

    /**
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="sender")
     */
    private $senderChats;

    /**
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="reciever")
     */
    private $recieverChats;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentChatRoom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

    public function __construct()
    {
        $this->ticket = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->ownersJobs = new ArrayCollection();
        $this->created = new \DateTime();
        $this->worksheets = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->featuredProfiles = new ArrayCollection();
        $this->featuredJobs = new ArrayCollection();
        $this->chatRooms = new ArrayCollection();
        $this->senderChats = new ArrayCollection();
        $this->recieverChats = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->firstName .' '. $this->lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicket(): Collection
    {
        return $this->ticket;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->ticket->contains($ticket)) {
            $this->ticket[] = $ticket;
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->ticket->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setUser($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getUser() === $this) {
                $answer->setUser(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(?string $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTariff(): ?Tariff
    {
        return $this->tariff;
    }

    public function setTariff(?Tariff $tariff): self
    {
        $this->tariff = $tariff;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setClient($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getClient() === $this) {
                $job->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getOwnersJobs(): Collection
    {
        return $this->ownersJobs;
    }

    public function addOwnersJob(Job $ownersJob): self
    {
        if (!$this->ownersJobs->contains($ownersJob)) {
            $this->ownersJobs[] = $ownersJob;
            $ownersJob->setOwner($this);
        }

        return $this;
    }

    public function removeOwnersJob(Job $ownersJob): self
    {
        if ($this->ownersJobs->removeElement($ownersJob)) {
            // set the owning side to null (unless already changed)
            if ($ownersJob->getOwner() === $this) {
                $ownersJob->setOwner(null);
            }
        }

        return $this;
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

    public function getLastlogin(): ?\DateTimeInterface
    {
        return $this->lastlogin;
    }

    public function setLastlogin(?\DateTimeInterface $lastlogin): self
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    /**
     * @return Collection<int, Worksheet>
     */
    public function getWorksheets(): Collection
    {
        return $this->worksheets;
    }

    public function addWorksheet(Worksheet $worksheet): self
    {
        if (!$this->worksheets->contains($worksheet)) {
            $this->worksheets[] = $worksheet;
            $worksheet->setUser($this);
        }

        return $this;
    }

    public function removeWorksheet(Worksheet $worksheet): self
    {
        if ($this->worksheets->removeElement($worksheet)) {
            // set the owning side to null (unless already changed)
            if ($worksheet->getUser() === $this) {
                $worksheet->setUser(null);
            }
        }

        return $this;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function isPhoneVerified(): ?bool
    {
        return $this->phoneVerified;
    }

    public function setPhoneVerified(bool $phoneVerified): self
    {
        $this->phoneVerified = $phoneVerified;

        return $this;
    }

    public function isPassportVerified(): ?bool
    {
        return $this->passportVerified;
    }

    public function setPassportVerified(bool $passportVerified): self
    {
        $this->passportVerified = $passportVerified;

        return $this;
    }

    public function getPreferredContactMethod(): ?string
    {
        return $this->preferredContactMethod;
    }

    public function setPreferredContactMethod(?string $preferredContactMethod): self
    {
        $this->preferredContactMethod = $preferredContactMethod;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Worksheet>
     */
    public function getFeaturedProfiles(): Collection
    {
        return $this->featuredProfiles;
    }

    public function addFeaturedProfile(Worksheet $featuredProfile): self
    {
        if (!$this->featuredProfiles->contains($featuredProfile)) {
            $this->featuredProfiles[] = $featuredProfile;
        }

        return $this;
    }

    public function removeFeaturedProfile(Worksheet $featuredProfile): self
    {
        $this->featuredProfiles->removeElement($featuredProfile);

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getFeaturedJobs(): Collection
    {
        return $this->featuredJobs;
    }

    public function addFeaturedJob(Job $featuredJob): self
    {
        if (!$this->featuredJobs->contains($featuredJob)) {
            $this->featuredJobs[] = $featuredJob;
        }

        return $this;
    }

    public function removeFeaturedJob(Job $featuredJob): self
    {
        $this->featuredJobs->removeElement($featuredJob);

        return $this;
    }

    public function isInactive(): ?bool
    {
        return $this->inactive;
    }

    public function setInactive(?bool $inactive): self
    {
        $this->inactive = $inactive;

        return $this;
    }

    public function getCitizen(): ?Citizen
    {
        return $this->citizen;
    }

    public function setCitizen(?Citizen $citizen): self
    {
        $this->citizen = $citizen;

        return $this;
    }

    /**
     * @return Collection<int, ChatRoom>
     */
    public function getChatRooms(): Collection
    {
        return $this->chatRooms;
    }

    public function addChatRoom(ChatRoom $chatRoom): self
    {
        if (!$this->chatRooms->contains($chatRoom)) {
            $this->chatRooms[] = $chatRoom;
            $chatRoom->addUser($this);
        }

        return $this;
    }

    public function removeChatRoom(ChatRoom $chatRoom): self
    {
        if ($this->chatRooms->removeElement($chatRoom)) {
            $chatRoom->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getSenderChats(): Collection
    {
        return $this->senderChats;
    }

    public function addSenderChat(Chat $senderChat): self
    {
        if (!$this->senderChats->contains($senderChat)) {
            $this->senderChats[] = $senderChat;
            $senderChat->setSender($this);
        }

        return $this;
    }

    public function removeSenderChat(Chat $senderChat): self
    {
        if ($this->senderChats->removeElement($senderChat)) {
            // set the owning side to null (unless already changed)
            if ($senderChat->getSender() === $this) {
                $senderChat->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getRecieverChats(): Collection
    {
        return $this->recieverChats;
    }

    public function addRecieverChat(Chat $recieverChat): self
    {
        if (!$this->recieverChats->contains($recieverChat)) {
            $this->recieverChats[] = $recieverChat;
            $recieverChat->setReciever($this);
        }

        return $this;
    }

    public function removeRecieverChat(Chat $recieverChat): self
    {
        if ($this->recieverChats->removeElement($recieverChat)) {
            // set the owning side to null (unless already changed)
            if ($recieverChat->getReciever() === $this) {
                $recieverChat->setReciever(null);
            }
        }

        return $this;
    }

    public function getCurrentChatRoom(): ?int
    {
        return $this->currentChatRoom;
    }

    public function setCurrentChatRoom(?int $currentChatRoom): self
    {
        $this->currentChatRoom = $currentChatRoom;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}

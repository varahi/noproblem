<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="jobs")
     */
    private $city;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $startNow;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="jobs")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="jobs")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ownersJobs")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=District::class, inversedBy="jobs")
     */
    private $district;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity=Experience::class, inversedBy="jobs")
     */
    private $experience;

    /**
     * @ORM\ManyToOne(targetEntity=Education::class, inversedBy="jobs")
     */
    private $education;

    /**
     * @ORM\ManyToOne(targetEntity=Citizen::class, inversedBy="jobs")
     */
    private $citizen;

    /**
     * @ORM\ManyToMany(targetEntity=Task::class, inversedBy="jobs")
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity=AdditionalInfo::class, inversedBy="jobs")
     */
    private $additional;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactFullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $anotherTask;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $anotherCitizen;

    /**
     * @ORM\OneToMany(targetEntity=Busyness::class, mappedBy="job")
     */
    private $busynesses;

    /**
     * @ORM\OneToMany(targetEntity=Accommodation::class, mappedBy="job")
     */
    private $accommodations;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->tasks = new ArrayCollection();
        $this->additional = new ArrayCollection();
        $this->busynesses = new ArrayCollection();
        $this->accommodations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

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

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function isStartNow(): ?bool
    {
        return $this->startNow;
    }

    public function setStartNow(?bool $startNow): self
    {
        $this->startNow = $startNow;

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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getEducation(): ?Education
    {
        return $this->education;
    }

    public function setEducation(?Education $education): self
    {
        $this->education = $education;

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
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        $this->tasks->removeElement($task);

        return $this;
    }

    /**
     * @return Collection<int, AdditionalInfo>
     */
    public function getAdditional(): Collection
    {
        return $this->additional;
    }

    public function addAdditional(AdditionalInfo $additional): self
    {
        if (!$this->additional->contains($additional)) {
            $this->additional[] = $additional;
        }

        return $this;
    }

    public function removeAdditional(AdditionalInfo $additional): self
    {
        $this->additional->removeElement($additional);

        return $this;
    }

    public function getContactFullName(): ?string
    {
        return $this->contactFullName;
    }

    public function setContactFullName(?string $contactFullName): self
    {
        $this->contactFullName = $contactFullName;

        return $this;
    }

    public function getAnotherTask(): ?string
    {
        return $this->anotherTask;
    }

    public function setAnotherTask(?string $anotherTask): self
    {
        $this->anotherTask = $anotherTask;

        return $this;
    }

    public function getAnotherCitizen(): ?string
    {
        return $this->anotherCitizen;
    }

    public function setAnotherCitizen(?string $anotherCitizen): self
    {
        $this->anotherCitizen = $anotherCitizen;

        return $this;
    }

    /**
     * @return Collection<int, Busyness>
     */
    public function getBusynesses(): Collection
    {
        return $this->busynesses;
    }

    public function addBusyness(Busyness $busyness): self
    {
        if (!$this->busynesses->contains($busyness)) {
            $this->busynesses[] = $busyness;
            $busyness->setJob($this);
        }

        return $this;
    }

    public function removeBusyness(Busyness $busyness): self
    {
        if ($this->busynesses->removeElement($busyness)) {
            // set the owning side to null (unless already changed)
            if ($busyness->getJob() === $this) {
                $busyness->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Accommodation>
     */
    public function getAccommodations(): Collection
    {
        return $this->accommodations;
    }

    public function addAccommodation(Accommodation $accommodation): self
    {
        if (!$this->accommodations->contains($accommodation)) {
            $this->accommodations[] = $accommodation;
            $accommodation->setJob($this);
        }

        return $this;
    }

    public function removeAccommodation(Accommodation $accommodation): self
    {
        if ($this->accommodations->removeElement($accommodation)) {
            // set the owning side to null (unless already changed)
            if ($accommodation->getJob() === $this) {
                $accommodation->setJob(null);
            }
        }

        return $this;
    }
}

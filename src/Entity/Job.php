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
     * @ORM\ManyToMany(targetEntity=Citizen::class, inversedBy="worksheets")
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
     * @ORM\OneToMany(targetEntity=Busyness::class, mappedBy="job") // ToDo: change relation to ManyToMany
     */
    private $busynesses;

    /**
     * @ORM\OneToMany(targetEntity=Accommodation::class, mappedBy="job", orphanRemoval=false) // ToDo: change relation to ManyToMany
     */
    private $accommodations;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=7, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=7, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="featuredJobs")
     */
    private $featuredUsers;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFree;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $paymentByHour;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $paymentByMonth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customBusynesses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passportSeries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passportNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $passportPlaceOfIssue;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $passportIssuingAuthority;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $passportDateOfIssue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passportPhoto;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->tasks = new ArrayCollection();
        $this->additional = new ArrayCollection();
        $this->busynesses = new ArrayCollection();
        $this->accommodations = new ArrayCollection();
        $this->featuredUsers = new ArrayCollection();
        $this->citizen = new ArrayCollection();
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

    /**
     * @return Collection<int, Citizen>
     */
    public function getCitizen(): Collection
    {
        return $this->citizen;
    }

    public function addCitizen(Citizen $citizen): self
    {
        if (!$this->citizen->contains($citizen)) {
            $this->citizen[] = $citizen;
        }

        return $this;
    }

    public function removeCitizen(Citizen $citizen): self
    {
        $this->citizen->removeElement($citizen);

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFeaturedUsers(): Collection
    {
        return $this->featuredUsers;
    }

    public function addFeaturedUser(User $featuredUser): self
    {
        if (!$this->featuredUsers->contains($featuredUser)) {
            $this->featuredUsers[] = $featuredUser;
            $featuredUser->addFeaturedJob($this);
        }

        return $this;
    }

    public function removeFeaturedUser(User $featuredUser): self
    {
        if ($this->featuredUsers->removeElement($featuredUser)) {
            $featuredUser->removeFeaturedJob($this);
        }

        return $this;
    }

    public function isIsFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(?bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function isPaymentByHour(): ?bool
    {
        return $this->paymentByHour;
    }

    public function setPaymentByHour(?bool $paymentByHour): self
    {
        $this->paymentByHour = $paymentByHour;

        return $this;
    }

    public function isPaymentByMonth(): ?bool
    {
        return $this->paymentByMonth;
    }

    public function setPaymentByMonth(?bool $paymentByMonth): self
    {
        $this->paymentByMonth = $paymentByMonth;

        return $this;
    }

    public function getCustomBusynesses(): ?string
    {
        return $this->customBusynesses;
    }

    public function setCustomBusynesses(?string $customBusynesses): self
    {
        $this->customBusynesses = $customBusynesses;

        return $this;
    }

    public function getPassportSeries(): ?string
    {
        return $this->passportSeries;
    }

    public function setPassportSeries(?string $passportSeries): self
    {
        $this->passportSeries = $passportSeries;

        return $this;
    }

    public function getPassportNumber(): ?string
    {
        return $this->passportNumber;
    }

    public function setPassportNumber(?string $passportNumber): self
    {
        $this->passportNumber = $passportNumber;

        return $this;
    }

    public function getPassportPlaceOfIssue(): ?string
    {
        return $this->passportPlaceOfIssue;
    }

    public function setPassportPlaceOfIssue(?string $passportPlaceOfIssue): self
    {
        $this->passportPlaceOfIssue = $passportPlaceOfIssue;

        return $this;
    }

    public function getPassportIssuingAuthority(): ?string
    {
        return $this->passportIssuingAuthority;
    }

    public function setPassportIssuingAuthority(?string $passportIssuingAuthority): self
    {
        $this->passportIssuingAuthority = $passportIssuingAuthority;

        return $this;
    }

    public function getPassportDateOfIssue(): ?\DateTimeInterface
    {
        return $this->passportDateOfIssue;
    }

    public function setPassportDateOfIssue(?\DateTimeInterface $passportDateOfIssue): self
    {
        $this->passportDateOfIssue = $passportDateOfIssue;

        return $this;
    }

    public function getPassportPhoto(): ?string
    {
        return $this->passportPhoto;
    }

    public function setPassportPhoto(?string $passportPhoto): self
    {
        $this->passportPhoto = $passportPhoto;

        return $this;
    }
}

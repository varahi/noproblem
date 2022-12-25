<?php

namespace App\Entity;

use App\Repository\WorksheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorksheetRepository::class)
 */
class Worksheet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="worksheets")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=District::class, inversedBy="worksheets")
     */
    private $district;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payment;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $startNow;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @ORM\ManyToOne(targetEntity=Education::class, inversedBy="worksheets")
     */
    private $education;

    /**
     * @ORM\ManyToOne(targetEntity=Experience::class, inversedBy="worksheets")
     */
    private $experience;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=AdditionalInfo::class, inversedBy="jobs")
     */
    private $additional;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $preferredContactMethod;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="worksheets")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="worksheets")
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=7, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=7, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\ManyToMany(targetEntity=Task::class, inversedBy="jobs")
     */
    private $tasks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactFullName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $paymentByAgreement;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $anotherTask;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Busyness::class, mappedBy="worksheet")
     */
    private $busynesses;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="featuredProfiles")
     */
    private $featuredUsers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

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
     * @ORM\ManyToOne(targetEntity=Accommodation::class, inversedBy="worksheets")
     */
    private $accommodations;

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

    /**
     * @ORM\ManyToMany(targetEntity=Citizen::class, inversedBy="worksheets")
     */
    private $citizen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientAge;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $amountOfChildren;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $additionalEducation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $yearsOfPractice;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDemo;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->additional = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->busynesses = new ArrayCollection();
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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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

    public function isStartNow(): ?bool
    {
        return $this->startNow;
    }

    public function setStartNow(?bool $startNow): self
    {
        $this->startNow = $startNow;

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

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

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

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): self
    {
        $this->experience = $experience;

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

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): self
    {
        $this->schedule = $schedule;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

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

    public function getContactFullName(): ?string
    {
        return $this->contactFullName;
    }

    public function setContactFullName(?string $contactFullName): self
    {
        $this->contactFullName = $contactFullName;

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

    public function isPaymentByAgreement(): ?bool
    {
        return $this->paymentByAgreement;
    }

    public function setPaymentByAgreement(?bool $paymentByAgreement): self
    {
        $this->paymentByAgreement = $paymentByAgreement;

        return $this;
    }

    public function getReviews(): ?string
    {
        return $this->reviews;
    }

    public function setReviews(?string $reviews): self
    {
        $this->reviews = $reviews;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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
            $busyness->setWorksheet($this);
        }

        return $this;
    }

    public function removeBusyness(Busyness $busyness): self
    {
        if ($this->busynesses->removeElement($busyness)) {
            // set the owning side to null (unless already changed)
            if ($busyness->getWorksheet() === $this) {
                $busyness->setWorksheet(null);
            }
        }

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
            $featuredUser->addFeaturedProfile($this);
        }

        return $this;
    }

    public function removeFeaturedUser(User $featuredUser): self
    {
        if ($this->featuredUsers->removeElement($featuredUser)) {
            $featuredUser->removeFeaturedProfile($this);
        }

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

    public function getAccommodations(): ?Accommodation
    {
        return $this->accommodations;
    }

    public function setAccommodations(?Accommodation $accommodations): self
    {
        $this->accommodations = $accommodations;

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

    public function getClientAge(): ?string
    {
        return $this->clientAge;
    }

    public function setClientAge(?string $clientAge): self
    {
        $this->clientAge = $clientAge;

        return $this;
    }

    public function getAmountOfChildren(): ?string
    {
        return $this->amountOfChildren;
    }

    public function setAmountOfChildren(?string $amountOfChildren): self
    {
        $this->amountOfChildren = $amountOfChildren;

        return $this;
    }

    public function getAdditionalEducation(): ?string
    {
        return $this->additionalEducation;
    }

    public function setAdditionalEducation(?string $additionalEducation): self
    {
        $this->additionalEducation = $additionalEducation;

        return $this;
    }

    public function getYearsOfPractice(): ?string
    {
        return $this->yearsOfPractice;
    }

    public function setYearsOfPractice(?string $yearsOfPractice): self
    {
        $this->yearsOfPractice = $yearsOfPractice;

        return $this;
    }

    public function isIsDemo(): ?bool
    {
        return $this->isDemo;
    }

    public function setIsDemo(?bool $isDemo): self
    {
        $this->isDemo = $isDemo;

        return $this;
    }
}

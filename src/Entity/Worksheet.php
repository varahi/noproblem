<?php

namespace App\Entity;

use App\Repository\WorksheetRepository;
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
     * @ORM\ManyToOne(targetEntity=Citizen::class, inversedBy="worksheets")
     */
    private $citizen;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hidden;

    public function __construct()
    {
        $this->created = new \DateTime();
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

    public function getCitizen(): ?Citizen
    {
        return $this->citizen;
    }

    public function setCitizen(?Citizen $citizen): self
    {
        $this->citizen = $citizen;

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

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(?string $additional): self
    {
        $this->additional = $additional;

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
}

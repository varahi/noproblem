<?php

namespace App\Entity;

use App\Repository\AccommodationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccommodationRepository::class)
 */
class Accommodation
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
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="accommodations")
     */
    private $job;

    /**
     * @ORM\OneToMany(targetEntity=Worksheet::class, mappedBy="accommodations")
     */
    private $worksheet;

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

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorksheet()
    {
        return $this->worksheet;
    }

    /**
     * @param mixed $worksheet
     */
    public function setWorksheet($worksheet): void
    {
        $this->worksheet = $worksheet;
    }
}

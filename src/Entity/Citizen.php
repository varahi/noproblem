<?php

namespace App\Entity;

use App\Repository\CitizenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CitizenRepository::class)
 */
class Citizen
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
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="citizen")
     */
    private $jobs;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="citizen")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=Worksheet::class, mappedBy="citizen")
     */
    private $worksheets;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->worksheets = new ArrayCollection();
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
            $job->setCitizen($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getCitizen() === $this) {
                $job->setCitizen(null);
            }
        }

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
            $user->setCitizen($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCitizen() === $this) {
                $user->setCitizen(null);
            }
        }

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
            $worksheet->addCitizen($this);
        }

        return $this;
    }

    public function removeWorksheet(Worksheet $worksheet): self
    {
        if ($this->worksheets->removeElement($worksheet)) {
            $worksheet->removeCitizen($this);
        }

        return $this;
    }
}

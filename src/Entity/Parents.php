<?php

namespace App\Entity;

use App\Repository\ParentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParentsRepository::class)]
class Parents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $referralSource = null; // Comment ils ont connu

    #[ORM\OneToMany(mappedBy: 'parents', targetEntity: Student::class)]
    private Collection $students;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(?string $fullName): self { $this->fullName = $fullName; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getReferralSource(): ?string { return $this->referralSource; }
    public function setReferralSource(?string $referralSource): self { $this->referralSource = $referralSource; return $this; }

    public function getStudents(): Collection { return $this->students; }
    public function addStudent(Student $student): self {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setParents($this);
        }
        return $this;
    }

    public function removeStudent(Student $student): self {
        if ($this->students->removeElement($student)) {
            if ($student->getParents() === $this) {
                $student->setParents(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->fullName ?? 'Parent #' . $this->id;
    }
}

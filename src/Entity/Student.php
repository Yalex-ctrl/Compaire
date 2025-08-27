<?php

namespace App\Entity;

use App\Enum\ClassLevel;
use App\Enum\StudentStatus;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(enumType: ClassLevel::class, nullable: true)]
    private ?ClassLevel $classLevel = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $subjects = []; // Jusqu’à 3 matières

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $weeklyHours = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $usualSchedule = null; // Exemple : Lundi 17h-18h, Mercredi 10h-12h

    #[ORM\Column(enumType: StudentStatus::class, nullable: true)]
    private ?StudentStatus $status = null;

    #[ORM\Column(enumType: StudentStatus::class, nullable: true)]
    private ?StudentStatus $convCompt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: Parents::class, inversedBy: 'students')]
    private ?Parents $parents = null;

    #[ORM\ManyToOne(targetEntity: Mentor::class, inversedBy: 'students')]
    private ?Mentor $mentor = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Course::class)]
    private Collection $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(?string $fullName): self { $this->fullName = $fullName; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getClassLevel(): ?ClassLevel { return $this->classLevel; }
    public function setClassLevel(?ClassLevel $classLevel): self { $this->classLevel = $classLevel; return $this; }

    public function getSubjects(): ?array { return $this->subjects; }
    public function setSubjects(?array $subjects): self { $this->subjects = $subjects; return $this; }

    public function getWeeklyHours(): ?int { return $this->weeklyHours; }
    public function setWeeklyHours(?int $weeklyHours): self { $this->weeklyHours = $weeklyHours; return $this; }

    public function getUsualSchedule(): ?string { return $this->usualSchedule; }
    public function setUsualSchedule(?string $usualSchedule): self { $this->usualSchedule = $usualSchedule; return $this; }

    public function getStatus(): ?StudentStatus { return $this->status; }
    public function setStatus(?StudentStatus $status): self { $this->status = $status; return $this; }

    public function getConvCompt(): ?StudentStatus { return $this->convCompt; }
    public function setConvCompt(?StudentStatus $convCompt): self { $this->convCompt = $convCompt; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getParents(): ?Parents { return $this->parents; }
    public function setParents(?Parents $parents): self { $this->parents = $parents; return $this; }

    public function getMentor(): ?Mentor { return $this->mentor; }
    public function setMentor(?Mentor $mentor): self { $this->mentor = $mentor; return $this; }

    public function getCourses(): Collection { return $this->courses; }
    public function addCourse(Course $course): self {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setStudent($this);
        }
        return $this;
    }
    public function removeCourse(Course $course): self {
        if ($this->courses->removeElement($course)) {
            if ($course->getStudent() === $this) {
                $course->setStudent(null);
            }
        }
        return $this;
    }

    
public function __toString(): string
{
    return $this->firstName . ' ' . $this->lastName; // ou autre champ représentatif
}
}

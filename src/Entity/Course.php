<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use App\Enum\Subject;
use App\Enum\CourseStatus;
use App\Enum\PaymentStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Mentor::class, inversedBy: 'courses')]
    private ?Mentor $mentor = null;

    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'courses')]
    private Collection $students;

    #[ORM\Column(enumType: Subject::class)]
    private Subject $subject;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $endTime;

    #[ORM\Column(enumType: CourseStatus::class)]
    private CourseStatus $status;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mentorNotes = null;

    #[ORM\Column(enumType: PaymentStatus::class)]
    private PaymentStatus $paymentStatus;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getMentor(): ?Mentor { return $this->mentor; }
    public function setMentor(?Mentor $mentor): self { $this->mentor = $mentor; return $this; }

    public function getStudents(): Collection { return $this->students; }
    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
        }
        return $this;
    }
    public function removeStudent(Student $student): self
    {
        $this->students->removeElement($student);
        return $this;
    }

    public function getSubjectLabel(): string
{
    return $this->subject ? $this->subject->value : '';
}

    public function getSubject(): Subject { return $this->subject; }
    public function setSubject(Subject $subject): self { $this->subject = $subject; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getStartTime(): \DateTimeInterface { return $this->startTime; }
    public function setStartTime(\DateTimeInterface $startTime): self { $this->startTime = $startTime; return $this; }

    public function getEndTime(): \DateTimeInterface { return $this->endTime; }
    public function setEndTime(\DateTimeInterface $endTime): self { $this->endTime = $endTime; return $this; }

    public function getStatus(): CourseStatus { return $this->status; }
    public function setStatus(CourseStatus $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }

    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }

    public function getMentorNotes(): ?string { return $this->mentorNotes; }
    public function setMentorNotes(?string $mentorNotes): self { $this->mentorNotes = $mentorNotes; return $this; }

    public function getPaymentStatus(): PaymentStatus { return $this->paymentStatus; }
    public function setPaymentStatus(PaymentStatus $paymentStatus): self { $this->paymentStatus = $paymentStatus; return $this; }
}

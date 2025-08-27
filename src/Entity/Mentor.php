<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Enum\AccountReportEnum;
use App\Enum\AutoStatusEnum;

#[ORM\Entity]
class Mentor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $lastName = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $firstName = null;

    #[ORM\ManyToMany(targetEntity: Student::class)]
    private Collection $students;

    #[ORM\Column(type:"string", length:20, nullable:true)]
    private ?string $phone = null;

    #[ORM\Column(type:"string", enumType: AccountReportEnum::class)]
    private AccountReportEnum $accountReport;

    #[ORM\Column(type:"string", enumType: AutoStatusEnum::class)]
    private AutoStatusEnum $autoStatus;

    #[ORM\Column(type:"text", nullable:true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->accountReport = AccountReportEnum::NO;
        $this->autoStatus = AutoStatusEnum::NO;
    }

    public function getId(): ?int { return $this->id; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getStudents(): Collection { return $this->students; }
    public function addStudent(Student $student): self
{
    if (!$this->students->contains($student)) {
        $this->students->add($student);
    }
    return $this;
}

public function removeStudent(Student $student): self
{
    $this->students->removeElement($student);
    return $this;
}

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getAccountReport(): AccountReportEnum { return $this->accountReport; }
    public function setAccountReport(AccountReportEnum $accountReport): self { $this->accountReport = $accountReport; return $this; }

    public function getAutoStatus(): AutoStatusEnum { return $this->autoStatus; }
    public function setAutoStatus(AutoStatusEnum $autoStatus): self { $this->autoStatus = $autoStatus; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function __toString(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}

<?php

namespace App\Entity;

use App\Repository\ActivityTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityTypeRepository::class)]
class ActivityType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $number_of_monitors = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNumberOfMonitors(): ?int
    {
        return $this->number_of_monitors;
    }

    public function setNumberOfMonitors(int $number_of_monitors): static
    {
        $this->number_of_monitors = $number_of_monitors;

        return $this;
    }
}

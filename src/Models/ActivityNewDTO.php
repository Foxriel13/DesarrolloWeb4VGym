<?php
// src/Models/ActivityNewDTO.php

namespace App\Models;

use Symfony\Component\Validator\Constraints as Assert;

class ActivityNewDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "La fecha de inicio es obligatoria")]
        public \DateTimeInterface $dateStart,

        #[Assert\NotBlank(message: "La fecha de fin es obligatoria")]
        public \DateTimeInterface $dateEnd,

        #[Assert\NotBlank(message: "El tipo de actividad es obligatorio")]
        public int $activityTypeId,

        #[Assert\NotBlank(message: "Los monitores son obligatorios")]
        public array $monitorsId
    ) {}
}

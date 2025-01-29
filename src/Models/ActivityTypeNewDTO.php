<?php
// src/Models/ActivityTypeNewDTO.php

namespace App\Models;

use Symfony\Component\Validator\Constraints as Assert;

class ActivityTypeNewDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "El nombre del tipo de actividad es obligatorio")]
        public string $name
    ) {}
}

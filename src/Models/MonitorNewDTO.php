<?php
// src/Models/MonitorNewDTO.php

namespace App\Models;

use Symfony\Component\Validator\Constraints as Assert;

class MonitorNewDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "El nombre del monitor es obligatorio")]
        public string $name,

        #[Assert\NotBlank(message: "El correo electrónico del monitor es obligatorio")]
        #[Assert\Email(message: "El correo electrónico no es válido")]
        public string $email,

        #[Assert\NotBlank(message: "El teléfono del monitor es obligatorio")]
        public string $phone,

        #[Assert\NotBlank(message: "La foto del monitor es obligatoria")]
        public string $photo
    ) {}
}

<?php
// src/DTO/ActivityTypeDTO.php

namespace App\Models;

class ActivityTypeDTO
{
    public int $id;
    public string $name;
    public int $number_monitors;

    // Constructor opcional si quieres inicializar propiedades
    public function __construct(int $id, string $name, int $number_monitors)
    {
        $this->id = $id;
        $this->name = $name;
        $this->number_monitors = $number_monitors;
    }
}

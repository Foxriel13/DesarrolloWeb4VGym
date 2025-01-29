<?php
// src/Models/ActivityDTO.php

namespace App\Models;

class ActivityDTO
{
    public int $id;
    public \DateTimeInterface $date_start;
    public \DateTimeInterface $date_end;
    public ActivityTypeDTO $activity_type; // Usamos ActivityTypeDTO para el tipo de actividad
    public array $monitors; // Cada monitor será un array de MonitorDTO

    public function __construct(
        int $id,
        \DateTimeInterface $date_start,
        \DateTimeInterface $date_end,
        ActivityTypeDTO $activity_type,
        array $monitors
    ) {
        $this->id = $id;
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->activity_type = $activity_type;
        $this->monitors = $monitors;
    }
}

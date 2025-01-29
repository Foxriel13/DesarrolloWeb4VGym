<?php
// src/DTO/MonitorDTO.php

namespace App\Models;

class MonitorDTO
{
    public int $id;
    public string $name;
    public string $email;
    public string $phone;
    public string $photo;

    // Constructor opcional si quieres inicializar propiedades
    public function __construct(int $id, string $name, string $email, string $phone, string $photo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->photo = $photo;
    }
}

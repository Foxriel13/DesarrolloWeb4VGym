<?php
// src/Service/MonitorService.php

// src/Service/MonitorService.php

namespace App\Service;

use App\Entity\Monitor;
use App\Repository\MonitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MonitorsService
{
    private $monitorRepository;
    private $entityManager;

    public function __construct(MonitorRepository $monitorRepository, EntityManagerInterface $entityManager)
    {
        $this->monitorRepository = $monitorRepository;
        $this->entityManager = $entityManager;
    }

    // Método para crear un nuevo monitor
    public function createMonitor(string $name, string $email, string $phone, string $photo)
    {
        $monitor = new Monitor();
        $monitor->setName($name);
        $monitor->setEmail($email);
        $monitor->setPhone($phone);
        $monitor->setPhoto($photo);

        $this->entityManager->persist($monitor);
        $this->entityManager->flush();

        return $monitor;
    }

    // Método para obtener todos los monitores
    public function getAllMonitors()
    {
        return $this->monitorRepository->findAll();
    }

    // Método para obtener un monitor por su ID
    public function getMonitorById(int $id): ?Monitor
    {
        $monitor = $this->monitorRepository->find($id);

        if (!$monitor) {
            throw new NotFoundHttpException('Monitor not found');
        }

        return $monitor;
    }

    // Método para actualizar un monitor
    public function updateMonitor(Monitor $monitor, string $name, string $email, string $phone, string $photo)
    {
        $monitor->setName($name);
        $monitor->setEmail($email);
        $monitor->setPhone($phone);
        $monitor->setPhoto($photo);

        $this->entityManager->flush();

        return $monitor;
    }

    // Método para eliminar un monitor
    public function deleteMonitor(Monitor $monitor)
    {
        $this->entityManager->remove($monitor);
        $this->entityManager->flush();
    }
}

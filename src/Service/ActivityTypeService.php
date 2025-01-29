<?php
// src/Service/ActivityTypeService.php

namespace App\Service;

use App\Entity\ActivityType;
use App\Repository\ActivityTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityTypeService
{
    private $activityTypeRepository;
    private $entityManager;

    public function __construct(ActivityTypeRepository $activityTypeRepository, EntityManagerInterface $entityManager)
    {
        $this->activityTypeRepository = $activityTypeRepository;
        $this->entityManager = $entityManager;
    }

    // Método para crear un nuevo tipo de actividad
    public function createActivityType(string $name)
    {
        $activityType = new ActivityType();
        $activityType->setName($name);

        // Persistir el tipo de actividad
        $this->entityManager->persist($activityType);
        $this->entityManager->flush();

        return $activityType;
    }

    // Método para obtener todos los tipos de actividad
    public function getAllActivityTypes()
    {
        return $this->activityTypeRepository->findAll();
    }

    // Método para obtener un tipo de actividad por su ID
    public function getActivityTypeById(int $id): ?ActivityType
    {
        $activityType = $this->activityTypeRepository->find($id);

        if (!$activityType) {
            throw new NotFoundHttpException('ActivityType not found');
        }

        return $activityType;
    }

    // Método para actualizar un tipo de actividad
    public function updateActivityType(ActivityType $activityType, string $name)
    {
        $activityType->setName($name);

        $this->entityManager->flush();

        return $activityType;
    }

    // Método para eliminar un tipo de actividad
    public function deleteActivityType(ActivityType $activityType)
    {
        $this->entityManager->remove($activityType);
        $this->entityManager->flush();
    }
}

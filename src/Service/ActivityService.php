<?php
// src/Service/ActivityService.php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Entity\Monitor;
use App\Repository\ActivityRepository;
use App\Repository\ActivityTypeRepository;
use App\Repository\MonitorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityService
{
    private ActivityRepository $activityRepository;
    private ActivityTypeRepository $activityTypeRepository;
    private MonitorRepository $monitorRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ActivityRepository $activityRepository,
        ActivityTypeRepository $activityTypeRepository,
        MonitorRepository $monitorRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->activityRepository = $activityRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->monitorRepository = $monitorRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllActivities(): array
    {
        return $this->activityRepository->findAll();
    }

    public function createActivity(\DateTimeInterface $dateStart, \DateTimeInterface $dateEnd, int $activityTypeId, array $monitorsIds): Activity
    {
        $activityType = $this->activityTypeRepository->find($activityTypeId);
        if (!$activityType) {
            throw new NotFoundHttpException('ActivityType not found');
        }

        $activity = new Activity();
        $activity->setDateStart($dateStart);
        $activity->setDateEnd($dateEnd);
        $activity->setActivityType($activityType);

        foreach ($monitorsIds as $monitorId) {
            $monitor = $this->monitorRepository->find($monitorId);
            if ($monitor) {
                $activity->addMonitor($monitor);
            }
        }

        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        return $activity;
    }

    public function updateActivity(Activity $activity, \DateTimeInterface $dateStart, \DateTimeInterface $dateEnd, int $activityTypeId, array $monitorsIds): Activity
    {
        $activityType = $this->activityTypeRepository->find($activityTypeId);
        if (!$activityType) {
            throw new NotFoundHttpException('ActivityType not found');
        }

        $activity->setDateStart($dateStart);
        $activity->setDateEnd($dateEnd);
        $activity->setActivityType($activityType);

        // Limpiar monitores antiguos y añadir los nuevos
        $activity->getMonitors()->clear();
        foreach ($monitorsIds as $monitorId) {
            $monitor = $this->monitorRepository->find($monitorId);
            if ($monitor) {
                $activity->addMonitor($monitor);
            }
        }

        $this->entityManager->flush();

        return $activity;
    }

    public function deleteActivity(Activity $activity): void
    {
        $this->entityManager->remove($activity);
        $this->entityManager->flush();
    }

    public function getActivityById(int $id): ?Activity
    {
        return $this->activityRepository->find($id);
    }
}

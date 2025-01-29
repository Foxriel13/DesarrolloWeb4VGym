<?php


namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ActivityType;
use App\Entity\Monitor;
use App\Models\ActivityDTO;
use App\Models\ActivityNewDTO;
use App\Models\ActivityTypeDTO;
use App\Models\MonitorDTO;
use App\Service\ActivityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActivityController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ActivityService $activityService;
    private ValidatorInterface $validator;

    // Inyectamos el EntityManagerInterface, ActivityService y ValidatorInterface en el constructor
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivityService $activityService,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->activityService = $activityService;
        $this->validator = $validator;
    }

    // Ruta para obtener todas las actividades (GET)
    #[Route('/activities', methods: ['GET'])]
    public function findAll(): JsonResponse
    {
        $activities = $this->activityService->getAllActivities();

        $data = [];
        foreach ($activities as $activity) {
            // Solo extraemos los nombres de los monitores
            $monitors = array_map(function (Monitor $monitor) {
                return $monitor->getName(); // Solo devolvemos el nombre del monitor
            }, $activity->getMonitors()->toArray());

            // Formateamos las fechas para que solo contengan la fecha sin la hora
            $dateStart = $activity->getDateStart()->format('Y-m-d'); // Solo la fecha
            $dateEnd = $activity->getDateEnd()->format('Y-m-d'); // Solo la fecha

            // Preparamos los datos en el formato deseado
            $data[] = [
                'id' => $activity->getId(),
                'activity_type' => $activity->getActivityType()->getName(),
                'number_monitors' => count($monitors), // Número de monitores
                'monitors' => $monitors, // Solo los nombres de los monitores
                'date_start' => $dateStart,
                'date_end' => $dateEnd
            ];
        }

        // Devolvemos los datos en formato JSON
        return $this->json($data);
    }

    // Ruta para crear una nueva actividad (POST)
    #[Route('/activities', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Crear el DTO de la actividad con los datos recibidos
        $activityNewDTO = new ActivityNewDTO(
            new \DateTime($data['date_start']),
            new \DateTime($data['date_end']),
            $data['activity_type_id'],
            $data['monitors_id']
        );

        // Validar el DTO
        $errors = $this->validator->validate($activityNewDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Crear la actividad utilizando el servicio
        $activity = $this->activityService->createActivity(
            $activityNewDTO->dateStart,
            $activityNewDTO->dateEnd,
            $activityNewDTO->activityTypeId,
            $activityNewDTO->monitorsId
        );

        // Mapear la actividad creada a un DTO (para respuesta)
        $monitors = array_map(function ($monitor) {
            return $monitor->getName(); // Solo devolvemos el nombre del monitor
        }, $activity->getMonitors()->toArray());

        // Preparamos los datos en el formato deseado
        $activityDTO = [
            'id' => $activity->getId(),
            'activity_type' => $activity->getActivityType()->getName(),
            'number_monitors' => count($monitors),
            'monitors' => $monitors,
            'date_start' => $activity->getDateStart()->format('Y-m-d'),
            'date_end' => $activity->getDateEnd()->format('Y-m-d')
        ];

        return $this->json($activityDTO, JsonResponse::HTTP_CREATED);
    }

    // Ruta para actualizar una actividad (PUT)
    #[Route('/activities/{activityId}', methods: ['PUT'])]
    public function update(int $activityId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $activity = $this->activityService->getActivityById($activityId);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], Response::HTTP_NOT_FOUND);
        }

        // Crear el DTO de la actividad para actualización (Usamos ActivityNewDTO aquí)
        $activityNewDTO = new ActivityNewDTO(
            new \DateTime($data['date_start']),
            new \DateTime($data['date_end']),
            $data['activity_type_id'],
            $data['monitors_id']
        );

        // Validar el DTO
        $errors = $this->validator->validate($activityNewDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Actualizar la actividad utilizando el servicio
        $updatedActivity = $this->activityService->updateActivity(
            $activity,
            $activityNewDTO->dateStart,
            $activityNewDTO->dateEnd,
            $activityNewDTO->activityTypeId,
            $activityNewDTO->monitorsId
        );

        // Solo extraemos los nombres de los monitores
        $monitors = array_map(function (Monitor $monitor) {
            return $monitor->getName(); // Solo devolvemos el nombre del monitor
        }, $updatedActivity->getMonitors()->toArray());

        // Preparamos los datos en el formato deseado
        $activityDTO = [
            'id' => $updatedActivity->getId(),
            'activity_type' => $updatedActivity->getActivityType()->getName(),
            'number_monitors' => count($monitors),
            'monitors' => $monitors,
            'date_start' => $updatedActivity->getDateStart()->format('Y-m-d'),
            'date_end' => $updatedActivity->getDateEnd()->format('Y-m-d')
        ];

        return $this->json($activityDTO);
    }

    // Ruta para eliminar una actividad (DELETE)
    #[Route('/activities/{activityId}', methods: ['DELETE'])]
    public function delete(int $activityId): JsonResponse
    {
        $activity = $this->activityService->getActivityById($activityId);

        if (!$activity) {
            return new JsonResponse(['error' => 'Activity not found'], Response::HTTP_NOT_FOUND);
        }

        // Eliminar la actividad utilizando el servicio
        $this->activityService->deleteActivity($activity);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

// src/Controller/ActivityTypeController.php

namespace App\Controller;

use App\Service\ActivityTypeService;
use App\Models\ActivityTypeNewDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActivityTypeController extends AbstractController
{
    private $activityTypeService;
    private $validator;

    public function __construct(ActivityTypeService $activityTypeService, ValidatorInterface $validator)
    {
        $this->activityTypeService = $activityTypeService;
        $this->validator = $validator;
    }

    // Ruta para obtener todos los tipos de actividad (GET)
    #[Route('/activity-types', methods: ['GET'])]
    public function getActivityTypes(): JsonResponse
    {
        $activityTypes = $this->activityTypeService->getAllActivityTypes();

        $activityTypeData = [];
        foreach ($activityTypes as $activityType) {
            $activityTypeData[] = [
                'id' => $activityType->getId(),
                'name' => $activityType->getName(),
            ];
        }

        return $this->json($activityTypeData);
    }

    // Ruta para crear un nuevo tipo de actividad (POST)
    #[Route('/activity-types', methods: ['POST'])]
    public function createActivityType(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Crear el DTO de ActivityType y validarlo
        $activityTypeNewDTO = new ActivityTypeNewDTO($data['name']);
        $errors = $this->validator->validate($activityTypeNewDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Llamar al servicio para crear el tipo de actividad
        $activityType = $this->activityTypeService->createActivityType($activityTypeNewDTO->name);

        return $this->json([
            'id' => $activityType->getId(),
            'name' => $activityType->getName(),
        ], JsonResponse::HTTP_CREATED);
    }
}

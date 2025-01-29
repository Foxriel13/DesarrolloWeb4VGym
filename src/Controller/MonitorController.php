<?php

// src/Controller/MonitorController.php

namespace App\Controller;

use App\Entity\Monitor;
use App\Models\MonitorNewDTO;
use App\Service\MonitorsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MonitorController extends AbstractController
{
    private $entityManager;
    private $validator;

    // Inyectamos el EntityManagerInterface y ValidatorInterface en el constructor
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    // Ruta para obtener todos los monitores (GET)
    #[Route('/monitors', methods: ['GET'])]
    public function getMonitors(MonitorsService $monitorService): JsonResponse
    {
        $monitors = $monitorService->getAllMonitors();

        $monitorData = [];
        foreach ($monitors as $monitor) {
            $monitorData[] = [
                'id' => $monitor->getId(),
                'name' => $monitor->getName(),
                'email' => $monitor->getEmail(),
                'phone' => $monitor->getPhone(),
                'photo' => $monitor->getPhoto(),
            ];
        }

        return $this->json($monitorData);
    }

    // Ruta para crear un nuevo monitor (POST)
    #[Route('/monitors', methods: ['POST'])]
    public function createMonitor(Request $request, MonitorsService $monitorService): JsonResponse
    {
        // Decodificar el JSON de la solicitud
        $data = json_decode($request->getContent(), true);

        // Crear el DTO y validarlo
        $monitorNewDTO = new MonitorNewDTO(
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['photo']
        );

        // Validar el DTO
        $errors = $this->validator->validate($monitorNewDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Crear el monitor utilizando el servicio
        $monitor = $monitorService->createMonitor(
            $monitorNewDTO->name,
            $monitorNewDTO->email,
            $monitorNewDTO->phone,
            $monitorNewDTO->photo
        );

        // Devolver la respuesta con el monitor creado
        return $this->json([
            'id' => $monitor->getId(),
            'name' => $monitor->getName(),
            'email' => $monitor->getEmail(),
            'phone' => $monitor->getPhone(),
            'photo' => $monitor->getPhoto(),
        ], JsonResponse::HTTP_CREATED);
    }

    // Ruta para actualizar un monitor (PUT)
    #[Route('/monitors/{id}', methods: ['PUT'])]
    public function updateMonitor(int $id, Request $request, MonitorsService $monitorService): JsonResponse
    {
        // Decodificar el JSON de la solicitud
        $data = json_decode($request->getContent(), true);

        // Crear el DTO y validarlo
        $monitorUpdateDTO = new MonitorNewDTO(
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['photo']
        );

        // Validar el DTO
        $errors = $this->validator->validate($monitorUpdateDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Obtener el monitor por ID
        try {
            $monitor = $monitorService->getMonitorById($id);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(['error' => 'Monitor not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Actualizar los datos del monitor
        $monitorService->updateMonitor(
            $monitor,
            $monitorUpdateDTO->name,
            $monitorUpdateDTO->email,
            $monitorUpdateDTO->phone,
            $monitorUpdateDTO->photo
        );

        return $this->json([
            'id' => $monitor->getId(),
            'name' => $monitor->getName(),
            'email' => $monitor->getEmail(),
            'phone' => $monitor->getPhone(),
            'photo' => $monitor->getPhoto(),
        ]);
    }

    // Ruta para eliminar un monitor (DELETE)
    #[Route('/monitors/{id}', methods: ['DELETE'])]
    public function deleteMonitor(int $id, MonitorsService $monitorService): JsonResponse
    {
        // Obtener el monitor por ID
        try {
            $monitor = $monitorService->getMonitorById($id);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(['error' => 'Monitor not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Eliminar el monitor
        $monitorService->deleteMonitor($monitor);

        return new JsonResponse(['message' => 'Monitor deleted successfully'], JsonResponse::HTTP_NO_CONTENT);
    }
}

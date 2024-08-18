<?php

namespace App\Controller;

use App\Entity\Consulta;
use App\Entity\Beneficiario;
use App\Entity\Medico;
use App\Entity\Hospital;
use App\Repository\ConsultaRepository;
use App\Repository\BeneficiarioRepository;
use App\Repository\MedicoRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ConsultaController extends AbstractController
{
    #[Route('/consulta', methods: ['GET'])]
    public function index(ConsultaRepository $consultaRepository): JsonResponse
    {
        $consultas = $consultaRepository->findAll();

        $data = [];
        foreach ($consultas as $consulta) {
            $data[] = [
                'id' => $consulta->getId(),
                'data' => $consulta->getData()->format('Y-m-d'),
                'status' => $consulta->getStatus(),
                'beneficiario' => $consulta->getBeneficiario()->getNome(),
                'medico' => $consulta->getMedico()->getNome(),
                'hospital' => $consulta->getHospital()->getNome(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/consulta/store', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $entityManager, 
        BeneficiarioRepository $beneficiarioRepository, 
        MedicoRepository $medicoRepository,
        HospitalRepository $hospitalRepository
    ): JsonResponse
    {
        $dataConsulta = $request->request->get('data');
        $status = $request->request->getBoolean('status');
        $beneficiarioId = $request->request->get('beneficiario_id');
        $medicoId = $request->request->get('medico_id');
        $hospitalId = $request->request->get('hospital_id');

        if (!$dataConsulta || !$beneficiarioId || !$medicoId || !$hospitalId) {
            return new JsonResponse(['error' => 'Todos os campos são obrigatórios'], 400);
        }

        $beneficiario = $beneficiarioRepository->find($beneficiarioId);
        $medico = $medicoRepository->find($medicoId);
        $hospital = $hospitalRepository->find($hospitalId);

        if (!$beneficiario || !$medico || !$hospital) {
            return new JsonResponse(['error' => 'Beneficiário, Médico ou Hospital não encontrados'], 404);
        }

        $consulta = new Consulta();
        $consulta->setData(new \DateTime($dataConsulta));
        $consulta->setStatus($status);
        $consulta->setBeneficiario($beneficiario);
        $consulta->setMedico($medico);
        $consulta->setHospital($hospital);

        $entityManager->persist($consulta);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Consulta criada com sucesso!'], 201);
    }

    #[Route('/consulta/update/{id}', methods: ['PUT'])]
    public function update(
        $id, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        ConsultaRepository $consultaRepository, 
        BeneficiarioRepository $beneficiarioRepository, 
        MedicoRepository $medicoRepository,
        HospitalRepository $hospitalRepository
    ): JsonResponse
    {
        $consulta = $consultaRepository->find($id);

        if (!$consulta) {
            return new JsonResponse(['error' => 'Consulta não encontrada'], 404);
        }

        if ($consulta->getStatus() === true) {
            return new JsonResponse(['error' => 'Consultas concluídas não podem ser alteradas'], 403);
        }

        $dataConsulta = $request->request->get('data');
        $status = $request->request->getBoolean('status');
        $beneficiarioId = $request->request->get('beneficiario_id');
        $medicoId = $request->request->get('medico_id');
        $hospitalId = $request->request->get('hospital_id');

        if ($dataConsulta) {
            $consulta->setData(new \DateTime($dataConsulta));
        }

        if (isset($status)) {
            $consulta->setStatus($status);
        }

        if ($beneficiarioId) {
            $beneficiario = $beneficiarioRepository->find($beneficiarioId);
            if (!$beneficiario) {
                return new JsonResponse(['error' => 'Beneficiário não encontrado'], 404);
            }
            $consulta->setBeneficiario($beneficiario);
        }

        if ($medicoId) {
            $medico = $medicoRepository->find($medicoId);
            if (!$medico) {
                return new JsonResponse(['error' => 'Médico não encontrado'], 404);
            }
            $consulta->setMedico($medico);
        }

        if ($hospitalId) {
            $hospital = $hospitalRepository->find($hospitalId);
            if (!$hospital) {
                return new JsonResponse(['error' => 'Hospital não encontrado'], 404);
            }
            $consulta->setHospital($hospital);
        }

        $entityManager->persist($consulta);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Consulta atualizada com sucesso!'], 200);
    }

    #[Route('/consulta/destroy/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $entityManager, ConsultaRepository $consultaRepository): JsonResponse
    {
        $consulta = $consultaRepository->find($id);

        if (!$consulta) {
            return new JsonResponse(['error' => 'Consulta não encontrada'], 404);
        }

        if ($consulta->getStatus() === true) {
            return new JsonResponse(['error' => 'Consultas concluídas não podem ser excluídas'], 403);
        }

        $entityManager->remove($consulta);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Consulta excluída com sucesso!'], 200);
    }
}

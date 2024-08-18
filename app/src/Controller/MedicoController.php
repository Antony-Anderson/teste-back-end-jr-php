<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Entity\Hospital;
use App\Repository\MedicoRepository;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MedicoController extends AbstractController
{
    #[Route('/medico', methods: ['GET'])]
    public function index(MedicoRepository $medicoRepository): JsonResponse
    {
        $medicos = $medicoRepository->findAll();

        $data = [];
        foreach ($medicos as $medico) {
            $data[] = [
                'id' => $medico->getId(),
                'nome' => $medico->getNome(),
                'especialidade' => $medico->getEspecialidade(),
                'hospital' => $medico->getHospital()->getNome(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/medico/store', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, HospitalRepository $hospitalRepository): JsonResponse
    {
        $nome = $request->request->get('nome');
        $especialidade = $request->request->get('especialidade');
        $hospitalId = $request->request->get('hospital_id');

        if (!$nome || !$especialidade || !$hospitalId) {
            return new JsonResponse(['error' => 'Os campos são obrigatórios'], 400);
        }

        $hospital = $hospitalRepository->find($hospitalId);
        if (!$hospital) {
            return new JsonResponse(['error' => 'Hospital não encontrado'], 404);
        }

        $medico = new Medico();
        $medico->setNome($nome);
        $medico->setEspecialidade($especialidade);
        $medico->setHospital($hospital);

        $entityManager->persist($medico);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Médico criado com sucesso!'], 201);
    }

    #[Route('/medico/update/{id}', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $entityManager, MedicoRepository $medicoRepository, HospitalRepository $hospitalRepository): JsonResponse
    {
        $medico = $medicoRepository->find($id);

        if (!$medico) {
            return new JsonResponse(['error' => 'Médico não encontrado'], 404);
        }

        $nome = $request->request->get('nome');
        $especialidade = $request->request->get('especialidade');
        $hospitalId = $request->request->get('hospital_id');

        $medico->setNome($nome);
        $medico->setEspecialidade($especialidade);

        if ($hospitalId) {
            $hospital = $hospitalRepository->find($hospitalId);
            if (!$hospital) {
                return new JsonResponse(['error' => 'Hospital não encontrado'], 404);
            }
            $medico->setHospital($hospital);
        }

        $entityManager->persist($medico);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Médico atualizado com sucesso!'], 200);
    }

    #[Route('/medico/destroy/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $entityManager, MedicoRepository $medicoRepository): JsonResponse
    {
        $medico = $medicoRepository->find($id);

        if (!$medico) {
            return new JsonResponse(['error' => 'Médico não encontrado'], 404);
        }

        $entityManager->remove($medico);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Médico deletado com sucesso!'], 200);
    }
}

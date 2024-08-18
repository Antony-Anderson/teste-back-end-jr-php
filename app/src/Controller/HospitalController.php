<?php

namespace App\Controller;

use App\Entity\Hospital;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HospitalController extends AbstractController
{
    #[Route('/hospital', methods: ['GET'])]
    public function index(HospitalRepository $hospitalRepository): JsonResponse
    {
        $hospitais = $hospitalRepository->findAll();

        $data = [];
        foreach ($hospitais as $hospital) {
            $data[] = [
                'id' => $hospital->getId(),
                'nome' => $hospital->getNome(),
                'endereco' => $hospital->getEndereco(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/hospital/store', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $nome = $request->request->get('nome');
        $endereco = $request->request->get('endereco');

        if (!$nome || !$endereco) {
            return new JsonResponse(['error' => 'Os campos são obrigatórios'], 400);
        }

        $hospital = new Hospital();
        $hospital->setNome($nome);
        $hospital->setEndereco($endereco);

        $entityManager->persist($hospital);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Hospital criado com sucesso!'], 201);
    }

    #[Route('/hospital/update/{id}', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $entityManager, HospitalRepository $hospitalRepository): JsonResponse
    {
        $hospital = $hospitalRepository->find($id);

        if (!$hospital) {
            return new JsonResponse(['error' => 'Hospital não encontrado'], 404);
        }

        $nome = $request->request->get('nome');
        $endereco = $request->request->get('endereco');

        $hospital->setNome($nome);
        $hospital->setEndereco($endereco);
       
        $entityManager->persist($hospital);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Hospital atualizado com sucesso!'], 200);
    }

    #[Route('/hospital/destroy/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $entityManager, HospitalRepository $hospitalRepository): JsonResponse
    {
        $hospital = $hospitalRepository->find($id);

        if (!$hospital) {
            return new JsonResponse(['error' => 'Hospital não encontrado'], 404);
        }

        $entityManager->remove($hospital);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Hospital deletado com sucesso!'], 200);
    }
}

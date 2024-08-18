<?php

namespace App\Controller;

use App\Entity\Beneficiario;
use App\Repository\BeneficiarioRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class BeneficiarioController extends AbstractController
{
    #[Route('/beneficiario', methods: ['GET'])]
    public function index(BeneficiarioRepository $beneficiarioRepository): JsonResponse
    {
        $beneficiarios = $beneficiarioRepository->findAll();

        $data = [];
        foreach ($beneficiarios as $beneficiario) {
            $data[] = [
                'id' => $beneficiario->getId(),
                'nome' => $beneficiario->getNome(),
                'email' => $beneficiario->getEmail(),
                'data_nascimento' => $beneficiario->getDataNascimento()->format('Y-m-d'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/beneficiario/store', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $nome = $request->request->get('nome');
        $email = $request->request->get('email');
        $dataNascimento = $request->request->get('data_nascimento');
    
        if (!$nome || !$email || !$dataNascimento) {
            return new JsonResponse(['error' => 'Os campos são obrigatórios'], 400);
        }
    
        $dataNascimentoDateTime = new \DateTime($dataNascimento);
        $now = new \DateTime();
        $intervalo = $now->diff($dataNascimentoDateTime);
        if ($intervalo->y < 18 || ($intervalo->y == 18 && ($intervalo->m > 0 || $intervalo->d > 0))) {
            return new JsonResponse(['error' => 'Usuário precisa ter mais de 18 anos'], 400);
        }
    
        $beneficiarioRepository = $entityManager->getRepository(Beneficiario::class);
        $existingBeneficiario = $beneficiarioRepository->findOneBy(['email' => $email]);
    
        if ($existingBeneficiario) {
            return new JsonResponse(['error' => 'Email já está em uso'], 400);
        }
    
        $beneficiario = new Beneficiario();
        $beneficiario->setNome($nome);
        $beneficiario->setEmail($email);
        $beneficiario->setDataNascimento($dataNascimentoDateTime);
    
        $entityManager->persist($beneficiario);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Beneficiário criado com sucesso!'], 201);
    }
    

    #[Route('/beneficiario/update/{id}', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $entityManager, BeneficiarioRepository $beneficiarioRepository): JsonResponse
    {
        $beneficiario = $beneficiarioRepository->find($id);

        if (!$beneficiario) {
            return new JsonResponse(['error' => 'Beneficiário não encontrado'], 404);
        }

        $nome = $request->request->get('nome');
        $email = $request->request->get('email');
        $dataNascimento = $request->request->get('data_nascimento');

        if (!$nome || !$email || !$dataNascimento) {
            return new JsonResponse(['error' => 'Os campos são obrigatórios'], 400);
        }

        $existingBeneficiario = $beneficiarioRepository->findOneBy(['email' => $email]);
        if ($existingBeneficiario && $existingBeneficiario->getId() !== $beneficiario->getId()) {
            return new JsonResponse(['error' => 'Email já está em uso por outro beneficiário'], 400);
        }
    
        $dataNascimentoDateTime = new \DateTime($dataNascimento);
        $now = new \DateTime();
        $intervalo = $now->diff($dataNascimentoDateTime);
        if ($intervalo->y < 18 || ($intervalo->y == 18 && ($intervalo->m > 0 || $intervalo->d > 0))) {
            return new JsonResponse(['error' => 'Usuário precisa ter mais de 18 anos'], 400);
        }

        $dataNascimentoDateTime = new \DateTime($dataNascimento);
    
        $beneficiario->setNome($nome);
        $beneficiario->setEmail($email);
        $beneficiario->setDataNascimento($dataNascimentoDateTime);

        $entityManager->persist($beneficiario);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Beneficiário atualizado com sucesso!'], 200);
    }

    #[Route('/beneficiario/destroy/{id}', methods: ['DELETE'])]
    public function delete($id, EntityManagerInterface $entityManager, BeneficiarioRepository $beneficiarioRepository): JsonResponse
    {
        $beneficiario = $beneficiarioRepository->find($id);

        if (!$beneficiario) {
            return new JsonResponse(['error' => 'Beneficiário não encontrado'], 404);
        }

        $entityManager->remove($beneficiario);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Beneficiário deletado com sucesso!'], 200);
    }
}

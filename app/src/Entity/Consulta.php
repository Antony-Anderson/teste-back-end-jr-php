<?php

namespace App\Entity;

use App\Repository\ConsultaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsultaRepository::class)]
class Consulta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $data = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Beneficiario $beneficiario = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Medico $Medico = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hospital $hospital = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBeneficiario(): ?Beneficiario
    {
        return $this->beneficiario;
    }

    public function setBeneficiario(?Beneficiario $beneficiario): static
    {
        $this->beneficiario = $beneficiario;

        return $this;
    }

    public function getMedico(): ?Medico
    {
        return $this->Medico;
    }

    public function setMedico(?Medico $Medico): static
    {
        $this->Medico = $Medico;

        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): static
    {
        $this->hospital = $hospital;

        return $this;
    }
}

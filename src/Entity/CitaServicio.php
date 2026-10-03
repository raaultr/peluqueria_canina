<?php

namespace App\Entity;

use App\Repository\CitaServicioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitaServicioRepository::class)]
class CitaServicio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precioAplicado = null;

    #[ORM\Column]
    private ?int $duracionAplicada = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observaciones = null;

    #[ORM\ManyToOne(inversedBy: 'citaServicios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cita $cita = null;

    #[ORM\ManyToOne(inversedBy: 'citaServicios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Servicio $servicio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrecioAplicado(): ?string
    {
        return $this->precioAplicado;
    }

    public function setPrecioAplicado(string $precioAplicado): static
    {
        $this->precioAplicado = $precioAplicado;

        return $this;
    }

    public function getDuracionAplicada(): ?int
    {
        return $this->duracionAplicada;
    }

    public function setDuracionAplicada(int $duracionAplicada): static
    {
        $this->duracionAplicada = $duracionAplicada;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): static
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getCita(): ?Cita
    {
        return $this->cita;
    }

    public function setCita(?Cita $cita): static
    {
        $this->cita = $cita;

        return $this;
    }

    public function getServicio(): ?Servicio
    {
        return $this->servicio;
    }

    public function setServicio(?Servicio $servicio): static
    {
        $this->servicio = $servicio;

        return $this;
    }
}

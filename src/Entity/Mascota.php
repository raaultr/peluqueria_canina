<?php

namespace App\Entity;

use App\Repository\MascotaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MascotaRepository::class)]
class Mascota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $raza = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $sexo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $fechaNacimiento = null;

    #[ORM\Column(nullable: true)]
    private ?float $peso = null;

    #[ORM\Column(length: 10)]
    private ?string $tamano = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $nota = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getRaza(): ?string
    {
        return $this->raza;
    }

    public function setRaza(string $raza): static
    {
        $this->raza = $raza;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): static
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTime
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(?\DateTime $fechaNacimiento): static
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(?float $peso): static
    {
        $this->peso = $peso;

        return $this;
    }

    public function getTamano(): ?string
    {
        return $this->tamano;
    }

    public function setTamano(string $tamano): static
    {
        $this->tamano = $tamano;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getNota(): ?string
    {
        return $this->nota;
    }

    public function setNota(?string $nota): static
    {
        $this->nota = $nota;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

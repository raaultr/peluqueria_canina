<?php

namespace App\Entity;

use App\Repository\CitaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CitaRepository::class)]
class Cita
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $inicio = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $fin = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notas = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'citasComoTrabajador')]
    private ?User $trabajador = null;

    #[ORM\ManyToOne(inversedBy: 'citas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mascota $mascota = null;

    /**
     * @var Collection<int, Pago>
     */
    #[ORM\OneToMany(targetEntity: Pago::class, mappedBy: 'cita')]
    private Collection $pagos;

    /**
     * @var Collection<int, CitaServicio>
     */
    #[ORM\OneToMany(targetEntity: CitaServicio::class, mappedBy: 'cita')]
    private Collection $citaServicios;

    public function __construct()
    {
        $this->pagos = new ArrayCollection();
        $this->citaServicios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInicio(): ?\DateTime
    {
        return $this->inicio;
    }

    public function setInicio(?\DateTime $inicio): static
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setFin(?\DateTime $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getNotas(): ?string
    {
        return $this->notas;
    }

    public function setNotas(?string $notas): static
    {
        $this->notas = $notas;

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

    public function getTrabajador(): ?User
    {
        return $this->trabajador;
    }

    public function setTrabajador(?User $trabajador): static
    {
        $this->trabajador = $trabajador;

        return $this;
    }

    public function getMascota(): ?Mascota
    {
        return $this->mascota;
    }

    public function setMascota(?Mascota $mascota): static
    {
        $this->mascota = $mascota;

        return $this;
    }

    /**
     * @return Collection<int, Pago>
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pago $pago): static
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos->add($pago);
            $pago->setCita($this);
        }

        return $this;
    }

    public function removePago(Pago $pago): static
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getCita() === $this) {
                $pago->setCita(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CitaServicio>
     */
    public function getCitaServicios(): Collection
    {
        return $this->citaServicios;
    }

    public function addCitaServicio(CitaServicio $citaServicio): static
    {
        if (!$this->citaServicios->contains($citaServicio)) {
            $this->citaServicios->add($citaServicio);
            $citaServicio->setCita($this);
        }

        return $this;
    }

    public function removeCitaServicio(CitaServicio $citaServicio): static
    {
        if ($this->citaServicios->removeElement($citaServicio)) {
            // set the owning side to null (unless already changed)
            if ($citaServicio->getCita() === $this) {
                $citaServicio->setCita(null);
            }
        }

        return $this;
    }
}

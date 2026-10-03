<?php

namespace App\Entity;

use App\Repository\VentaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VentaRepository::class)]
class Venta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $metodoPago = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observaciones = null;

    /**
     * @var Collection<int, VentaDetalles>
     */
    #[ORM\OneToMany(targetEntity: VentaDetalles::class, mappedBy: 'venta')]
    private Collection $ventaDetalles;

    #[ORM\ManyToOne(inversedBy: 'ventasComoCliente')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $cliente = null;

    #[ORM\ManyToOne(inversedBy: 'ventasComoTrabajador')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $trabajador = null;

    public function __construct()
    {
        $this->ventaDetalles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMetodoPago(): ?string
    {
        return $this->metodoPago;
    }

    public function setMetodoPago(string $metodoPago): static
    {
        $this->metodoPago = $metodoPago;

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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): static
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * @return Collection<int, VentaDetalles>
     */
    public function getVentaDetalles(): Collection
    {
        return $this->ventaDetalles;
    }

    public function addVentaDetalle(VentaDetalles $ventaDetalle): static
    {
        if (!$this->ventaDetalles->contains($ventaDetalle)) {
            $this->ventaDetalles->add($ventaDetalle);
            $ventaDetalle->setVenta($this);
        }

        return $this;
    }

    public function removeVentaDetalle(VentaDetalles $ventaDetalle): static
    {
        if ($this->ventaDetalles->removeElement($ventaDetalle)) {
            // set the owning side to null (unless already changed)
            if ($ventaDetalle->getVenta() === $this) {
                $ventaDetalle->setVenta(null);
            }
        }

        return $this;
    }

    public function getCliente(): ?User
    {
        return $this->cliente;
    }

    public function setCliente(?User $cliente): static
    {
        $this->cliente = $cliente;

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
}

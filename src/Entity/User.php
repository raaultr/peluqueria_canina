<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 150)]
    private ?string $apellido = null;

    #[ORM\Column(length: 20)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $direccion = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $activo = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, Venta>
     */
    #[ORM\OneToMany(targetEntity: Venta::class, mappedBy: 'cliente')]
    private Collection $ventasComoCliente;

    /**
     * @var Collection<int, Venta>
     */
    #[ORM\OneToMany(targetEntity: Venta::class, mappedBy: 'trabajador')]
    private Collection $ventasComoTrabajador;

    /**
     * @var Collection<int, Mascota>
     */
    #[ORM\OneToMany(targetEntity: Mascota::class, mappedBy: 'cliente')]
    private Collection $mascotas;

    /**
     * @var Collection<int, Cita>
     */
    #[ORM\OneToMany(targetEntity: Cita::class, mappedBy: 'trabajador')]
    private Collection $citasComoTrabajador;

    public function __construct()
    {
        $this->ventasComoCliente = new ArrayCollection();
        $this->ventasComoTrabajador = new ArrayCollection();
        $this->mascotas = new ArrayCollection();
        $this->citasComoTrabajador = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        
        return $data;
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): static
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

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

    /**
     * @return Collection<int, Venta>
     */
    public function getVentasComoCliente(): Collection
    {
        return $this->ventasComoCliente;
    }

    public function addVentasComoCliente(Venta $ventasComoCliente): static
    {
        if (!$this->ventasComoCliente->contains($ventasComoCliente)) {
            $this->ventasComoCliente->add($ventasComoCliente);
            $ventasComoCliente->setCliente($this);
        }

        return $this;
    }

    public function removeVentasComoCliente(Venta $ventasComoCliente): static
    {
        if ($this->ventasComoCliente->removeElement($ventasComoCliente)) {
            // set the owning side to null (unless already changed)
            if ($ventasComoCliente->getCliente() === $this) {
                $ventasComoCliente->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Venta>
     */
    public function getVentasComoTrabajador(): Collection
    {
        return $this->ventasComoTrabajador;
    }

    public function addVentasComoTrabajador(Venta $ventasComoTrabajador): static
    {
        if (!$this->ventasComoTrabajador->contains($ventasComoTrabajador)) {
            $this->ventasComoTrabajador->add($ventasComoTrabajador);
            $ventasComoTrabajador->setTrabajador($this);
        }

        return $this;
    }

    public function removeVentasComoTrabajador(Venta $ventasComoTrabajador): static
    {
        if ($this->ventasComoTrabajador->removeElement($ventasComoTrabajador)) {
            // set the owning side to null (unless already changed)
            if ($ventasComoTrabajador->getTrabajador() === $this) {
                $ventasComoTrabajador->setTrabajador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mascota>
     */
    public function getMascotas(): Collection
    {
        return $this->mascotas;
    }

    public function addMascota(Mascota $mascota): static
    {
        if (!$this->mascotas->contains($mascota)) {
            $this->mascotas->add($mascota);
            $mascota->setCliente($this);
        }

        return $this;
    }

    public function removeMascota(Mascota $mascota): static
    {
        if ($this->mascotas->removeElement($mascota)) {
            // set the owning side to null (unless already changed)
            if ($mascota->getCliente() === $this) {
                $mascota->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cita>
     */
    public function getCitasComoTrabajador(): Collection
    {
        return $this->citasComoTrabajador;
    }

    public function addCitasComoTrabajador(Cita $citasComoTrabajador): static
    {
        if (!$this->citasComoTrabajador->contains($citasComoTrabajador)) {
            $this->citasComoTrabajador->add($citasComoTrabajador);
            $citasComoTrabajador->setTrabajador($this);
        }

        return $this;
    }

    public function removeCitasComoTrabajador(Cita $citasComoTrabajador): static
    {
        if ($this->citasComoTrabajador->removeElement($citasComoTrabajador)) {
            // set the owning side to null (unless already changed)
            if ($citasComoTrabajador->getTrabajador() === $this) {
                $citasComoTrabajador->setTrabajador(null);
            }
        }

        return $this;
    }
}

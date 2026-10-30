<?php

namespace App\DataFixtures;

use App\Entity\Mascota;
use App\Entity\Cita;
use App\Entity\CitaServicio;
use App\Entity\Servicio;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $adminEmail = 'admin.demo@pelucan.test';
        $admin = $userRepository->findOneBy(['email' => $adminEmail]);

        if (!$admin) {
            $admin = $this->createUser($adminEmail, 'Admin', 'Demo', '600000000', ['ROLE_ADMIN']);
            $manager->persist($admin);
        }

        $clientData = [
            ['Ana', 'Torres', 'ana.torres@pelucan.test'],
            ['Carlos', 'Ruiz', 'carlos.ruiz@pelucan.test'],
            ['Sofia', 'Ramirez', 'sofia.ramirez@pelucan.test'],
            ['Diego', 'Mendoza', 'diego.mendoza@pelucan.test'],
            ['Valentina', 'Cruz', 'valentina.cruz@pelucan.test'],
            ['Lucia', 'Fernandez', 'lucia.fernandez@pelucan.test'],
        ];
        $clients = [];

        foreach ($clientData as [$firstName, $lastName, $email]) {
            $client = $userRepository->findOneBy(['email' => $email]);

            if (!$client) {
                $client = $this->createUser($email, $firstName, $lastName, '600000001', ['ROLE_CLIENTE']);
                $manager->persist($client);
            }

            $clients[$email] = $client;
        }

        $employeeData = [
            ['Carmen', 'Vega', 'carmen.vega@pelucan.test', '600000010'],
            ['Mateo', 'Rios', 'mateo.rios@pelucan.test', '600000011'],
            ['Lucia', 'Martin', 'lucia.martin@pelucan.test', '600000012'],
        ];
        $employees = [];

        foreach ($employeeData as [$firstName, $lastName, $email, $phone]) {
            $employee = $userRepository->findOneBy(['email' => $email]);

            if (!$employee) {
                $employee = $this->createUser($email, $firstName, $lastName, $phone, ['ROLE_EMPLEADO']);
                $manager->persist($employee);
            }

            $employees[$email] = $employee;
        }

        $petData = [
            ['Luna', 'Golden Retriever', 'Hembra', '2021-06-14', 28.4, 'Grande', 'ana.torres@pelucan.test'],
            ['Max', 'Bulldog Frances', 'Macho', '2022-10-04', 12.6, 'Mediano', 'ana.torres@pelucan.test'],
            ['Nala', 'Siamés', 'Hembra', '2023-02-11', 4.1, 'Pequeño', 'carlos.ruiz@pelucan.test'],
            ['Rocky', 'Border Collie', 'Macho', '2020-08-19', 21.3, 'Grande', 'carlos.ruiz@pelucan.test'],
            ['Bella', 'Cocker Spaniel', 'Hembra', '2019-12-02', 13.0, 'Mediano', 'sofia.ramirez@pelucan.test'],
            ['Toby', 'Maine Coon', 'Macho', '2023-04-22', 6.8, 'Mediano', 'sofia.ramirez@pelucan.test'],
            ['Kira', 'Labrador Retriever', 'Hembra', '2022-01-30', 25.7, 'Grande', 'diego.mendoza@pelucan.test'],
            ['Bruno', 'Beagle', 'Macho', '2021-09-07', 14.8, 'Mediano', 'diego.mendoza@pelucan.test'],
            ['Coco', 'Chihuahua', 'Hembra', '2024-03-16', 2.4, 'Pequeño', 'valentina.cruz@pelucan.test'],
            ['Simba', 'Persa', 'Macho', '2020-11-25', 5.6, 'Pequeño', 'valentina.cruz@pelucan.test'],
            ['Thor', 'Pastor Aleman', 'Macho', '2019-05-09', 34.0, 'Grande', 'lucia.fernandez@pelucan.test'],
            ['Lola', 'Caniche Toy', 'Hembra', '2024-07-03', 3.2, 'Pequeño', 'lucia.fernandez@pelucan.test'],
        ];
        $mascotaRepository = $manager->getRepository(Mascota::class);

        foreach ($petData as [$name, $breed, $sex, $birthday, $weight, $size, $clientEmail]) {
            $client = $clients[$clientEmail];
            if ($mascotaRepository->findOneBy(['nombre' => $name, 'cliente' => $client])) {
                continue;
            }

            $mascota = (new Mascota())
                ->setNombre($name)
                ->setRaza($breed)
                ->setSexo($sex)
                ->setFechaNacimiento(new \DateTime($birthday))
                ->setPeso($weight)
                ->setTamano($size)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setCliente($client);

            $manager->persist($mascota);
        }

        $serviceData = [
            ['Baño y secado', 'Baño completo con secado y cepillado.', 60],
            ['Corte higiénico', 'Aseo de zonas sensibles y repaso de uñas.', 35],
            ['Corte de raza', 'Corte adaptado a la raza y tipo de pelaje.', 75],
            ['Deslanado', 'Retirada de pelo muerto y cepillado profundo.', 50],
        ];
        $serviceRepository = $manager->getRepository(Servicio::class);
        $services = [];

        foreach ($serviceData as [$name, $description, $duration]) {
            $service = $serviceRepository->findOneBy(['nombre' => $name]);

            if (!$service) {
                $service = (new Servicio())
                    ->setNombre($name)
                    ->setDescripcion($description)
                    ->setDuracionMins($duration)
                    ->setActivo(true)
                    ->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($service);
            }

            $services[$name] = $service;
        }

        $appointmentData = [
            ['Luna', '2026-10-28 10:00', '2026-10-28 11:00', 'Realizada', 'Baño y secado'],
            ['Max', '2026-10-31 12:30', '2026-10-31 13:05', 'Confirmada', 'Corte higiénico'],
            ['Nala', '2026-11-02 09:30', '2026-11-02 10:45', 'Pendiente', 'Corte de raza'],
            ['Bella', '2026-11-04 16:00', '2026-11-04 16:50', 'Confirmada', 'Deslanado'],
            ['Kira', '2026-10-20 11:00', '2026-10-20 12:00', 'Realizada', 'Baño y secado'],
            ['Coco', '2026-11-06 15:00', '2026-11-06 15:35', 'Pendiente', 'Corte higiénico'],
        ];
        $mascotaRepository = $manager->getRepository(Mascota::class);
        $citaRepository = $manager->getRepository(Cita::class);

        foreach ($appointmentData as [$petName, $start, $end, $status, $serviceName]) {
            $mascota = $mascotaRepository->findOneBy(['nombre' => $petName]);
            if (!$mascota) {
                continue;
            }

            $startAt = new \DateTime($start);
            if ($citaRepository->findOneBy(['mascota' => $mascota, 'inicio' => $startAt])) {
                continue;
            }

            $cita = (new Cita())
                ->setInicio($startAt)
                ->setFin(new \DateTime($end))
                ->setEstado($status)
                ->setNotas('Cita de demostración')
                ->setCreatedAt(new \DateTimeImmutable())
                ->setTrabajador($admin)
                ->setMascota($mascota);
            $manager->persist($cita);

            $citaServicio = (new CitaServicio())
                ->setPrecioAplicado('35.00')
                ->setDuracionAplicada($services[$serviceName]->getDuracionMins())
                ->setCita($cita)
                ->setServicio($services[$serviceName]);
            $manager->persist($citaServicio);
        }

        $employeeAppointmentData = [
            ['Rocky', '2026-11-08 10:00', '2026-11-08 10:45', 'Confirmada', 'Corte higiénico', 'carmen.vega@pelucan.test'],
            ['Bruno', '2026-11-09 12:00', '2026-11-09 13:15', 'Pendiente', 'Corte de raza', 'mateo.rios@pelucan.test'],
            ['Lola', '2026-11-10 09:30', '2026-11-10 10:20', 'Confirmada', 'Deslanado', 'lucia.martin@pelucan.test'],
        ];

        foreach ($employeeAppointmentData as [$petName, $start, $end, $status, $serviceName, $employeeEmail]) {
            $mascota = $mascotaRepository->findOneBy(['nombre' => $petName]);
            if (!$mascota) {
                continue;
            }

            $startAt = new \DateTime($start);
            if ($citaRepository->findOneBy(['mascota' => $mascota, 'inicio' => $startAt])) {
                continue;
            }

            $cita = (new Cita())
                ->setInicio($startAt)
                ->setFin(new \DateTime($end))
                ->setEstado($status)
                ->setNotas('Cita de demostración para el equipo.')
                ->setCreatedAt(new \DateTimeImmutable())
                ->setTrabajador($employees[$employeeEmail])
                ->setMascota($mascota);
            $manager->persist($cita);

            $citaServicio = (new CitaServicio())
                ->setPrecioAplicado('35.00')
                ->setDuracionAplicada($services[$serviceName]->getDuracionMins())
                ->setCita($cita)
                ->setServicio($services[$serviceName]);
            $manager->persist($citaServicio);
        }

        $manager->flush();
    }

    private function createUser(string $email, string $firstName, string $lastName, string $phone, array $roles): User
    {
        $user = (new User())
            ->setEmail($email)
            ->setNombre($firstName)
            ->setApellido($lastName)
            ->setTelefono($phone)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setRoles($roles);

        $user->setPassword($this->passwordHasher->hashPassword($user, 'PeluCanDemo2026!'));

        return $user;
    }
}

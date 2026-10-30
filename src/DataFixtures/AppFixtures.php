<?php

namespace App\DataFixtures;

use App\Entity\Mascota;
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

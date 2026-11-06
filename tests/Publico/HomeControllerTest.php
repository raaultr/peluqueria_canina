<?php

namespace App\Tests\Publico;

use App\Entity\Servicio;
use App\Entity\ServicioTarifa;
use App\Repository\ServicioRepository;
use App\Repository\ServicioTarifaRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testLandingPageShowsItsMainSections(): void
    {
        $serviceDefinitions = [
            ['Baño y secado', 2],
            ['Corte de raza', 4],
            ['Corte higiénico', 4],
            ['Deslanado', 1],
        ];
        $services = [];
        $serviceRows = [];

        foreach ($serviceDefinitions as [$name, $requestCount]) {
            $service = (new Servicio())
                ->setNombre($name)
                ->setDescripcion('Descripción de ' . $name)
                ->setDuracionMins(45)
                ->setActivo(true)
                ->setCreatedAt(new \DateTimeImmutable());
            $services[] = $service;
            $serviceRows[] = [0 => $service, 'requestCount' => $requestCount];
        }

        $tariffs = [];
        foreach ($services as $service) {
            foreach (['Pequeño' => '28.00', 'Mediano' => '36.00', 'Grande' => '46.00'] as $size => $price) {
                $tariffs[] = (new ServicioTarifa())
                    ->setServicio($service)
                    ->setTamano($size)
                    ->setPrecio($price);
            }
        }

        $servicioRepository = $this->createMock(ServicioRepository::class);
        $servicioRepository->expects(self::once())
            ->method('findActiveWithRequestCounts')
            ->willReturn($serviceRows);

        $tarifaRepository = $this->createMock(ServicioTarifaRepository::class);
        $tarifaRepository->expects(self::once())
            ->method('findBy')
            ->with(['servicio' => $services])
            ->willReturn($tariffs);

        $client = static::createClient();
        static::getContainer()->set(ServicioRepository::class, $servicioRepository);
        static::getContainer()->set(ServicioTarifaRepository::class, $tarifaRepository);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Porque ellos también');
        self::assertSelectorTextContains('#servicios', 'Baño y secado');
        self::assertSelectorTextContains('#home-method-title', 'El Método PeluCan en 4 pasos');
        self::assertSelectorCount(4, '.home-price-card');
        self::assertSelectorCount(1, '.home-price-card.is-popular');
        self::assertSelectorTextContains('.home-price-card.is-popular h3', 'Corte de raza');
        self::assertSelectorExists('.home-price-card [data-service-price][data-small="28,00 €"]');
        self::assertSelectorExists('img[src*="golden-sinfondo-transparent.png"]');
    }
}
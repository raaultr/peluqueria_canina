<?php

namespace App\Controller\Publico;

use App\Entity\Servicio;
use App\Entity\ServicioTarifa;
use App\Repository\ServicioRepository;
use App\Repository\ServicioTarifaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_publico_home')]
    public function index(ServicioRepository $servicioRepository, ServicioTarifaRepository $tarifaRepository): Response
    {
        $serviceRows = $servicioRepository->findActiveWithRequestCounts();
        $mostRequestedService = null;
        $highestRequestCount = -1;
        $serviceIds = [];
        $ratesByService = [];

        foreach ($serviceRows as $row) {
            /** @var Servicio $service */
            $service = $row[0];
            $requestCount = (int) $row['requestCount'];
            $serviceIds[] = $service;
            $ratesByService[spl_object_id($service)] = ['small' => null, 'medium' => null, 'large' => null];

            if ($requestCount > $highestRequestCount) {
                $highestRequestCount = $requestCount;
                $mostRequestedService = $service;
            }
        }

        if ($serviceIds !== []) {
            $sizeKeys = ['Pequeño' => 'small', 'Mediano' => 'medium', 'Grande' => 'large'];

            foreach ($tarifaRepository->findBy(['servicio' => $serviceIds]) as $tarifa) {
                /** @var ServicioTarifa $tarifa */
                $sizeKey = $sizeKeys[$tarifa->getTamano()] ?? null;
                $service = $tarifa->getServicio();

                if ($sizeKey !== null && $service !== null) {
                    $ratesByService[spl_object_id($service)][$sizeKey] = $tarifa->getPrecio();
                }
            }
        }

        $catalogoServicios = array_map(static function (array $row) use ($mostRequestedService, $ratesByService): array {
            /** @var Servicio $service */
            $service = $row[0];

            return [
                'servicio' => $service,
                'tarifas' => $ratesByService[spl_object_id($service)],
                'popular' => $service === $mostRequestedService,
            ];
        }, $serviceRows);

        return $this->render('publico/home/index.html.twig', [
            'catalogoServicios' => $catalogoServicios,
        ]);
    }
}

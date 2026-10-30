<?php

namespace App\Controller\Admin;

use App\Entity\Servicio;
use App\Form\ServicioType;
use App\Repository\ServicioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/servicio')]
final class ServicioController extends AbstractController
{
    #[Route(name: 'app_servicio_index', methods: ['GET'])]
    public function index(ServicioRepository $servicioRepository): Response
    {
        return $this->renderIndex($servicioRepository);
    }

    #[Route('/new', name: 'app_servicio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ServicioRepository $servicioRepository): Response
    {
        $servicio = new Servicio();
        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $servicio->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($servicio);
            $entityManager->flush();

            return $this->redirectToRoute('app_servicio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($servicioRepository, [
            'title' => 'Nuevo servicio',
            'description' => 'Añade un servicio al catálogo de PeluCan.',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_servicio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Servicio $servicio, EntityManagerInterface $entityManager, ServicioRepository $servicioRepository): Response
    {
        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $servicio->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_servicio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($servicioRepository, [
            'title' => 'Editar servicio',
            'description' => 'Actualiza la información de este servicio.',
            'form' => $form->createView(),
        ]);
    }

    private function renderIndex(ServicioRepository $servicioRepository, ?array $modal = null): Response
    {
        return $this->render('admin/servicio/index.html.twig', [
            'servicios' => $servicioRepository->findBy([], ['nombre' => 'ASC']),
            'modal' => $modal ? $modal + [
                'closeUrl' => $this->generateUrl('app_servicio_index'),
                'formTemplate' => 'admin/servicio/_form.html.twig',
                'submitLabel' => 'Guardar servicio',
            ] : null,
        ]);
    }
}
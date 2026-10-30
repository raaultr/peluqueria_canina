<?php

namespace App\Controller\Admin;

use App\Entity\Cita;
use App\Form\CitaType;
use App\Repository\CitaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/cita')]
final class CitaController extends AbstractController
{
    #[Route(name: 'app_cita_index', methods: ['GET'])]
    public function index(CitaRepository $citaRepository): Response
    {
        return $this->renderIndex($citaRepository);
    }

    #[Route('/new', name: 'app_cita_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CitaRepository $citaRepository): Response
    {
        $cita = new Cita();
        $form = $this->createForm(CitaType::class, $cita);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cita->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($cita);
            $entityManager->flush();

            return $this->redirectToRoute('app_cita_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($citaRepository, [
            'title' => 'Nueva cita',
            'description' => 'Programa una cita y asigna la mascota correspondiente.',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cita_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cita $cita, EntityManagerInterface $entityManager, CitaRepository $citaRepository): Response
    {
        $form = $this->createForm(CitaType::class, $cita);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cita->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_cita_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($citaRepository, [
            'title' => 'Editar cita',
            'description' => 'Actualiza la programación y los datos de esta cita.',
            'form' => $form->createView(),
        ]);
    }

    private function renderIndex(CitaRepository $citaRepository, ?array $modal = null): Response
    {
        return $this->render('admin/cita/index.html.twig', [
            'citas' => $citaRepository->findBy([], ['inicio' => 'DESC']),
            'modal' => $modal ? $modal + [
                'closeUrl' => $this->generateUrl('app_cita_index'),
                'formTemplate' => 'admin/cita/_form.html.twig',
                'submitLabel' => 'Guardar cita',
            ] : null,
        ]);
    }
}
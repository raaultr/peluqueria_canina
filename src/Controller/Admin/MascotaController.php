<?php

namespace App\Controller\Admin;

use App\Entity\Mascota;
use App\Form\MascotaType;
use App\Repository\CitaRepository;
use App\Repository\MascotaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/mascota')]
final class MascotaController extends AbstractController
{
    #[Route(name: 'app_mascota_index', methods: ['GET'])]
    public function index(MascotaRepository $mascotaRepository): Response
    {
        return $this->render('admin/mascota/index.html.twig', [
            'mascotas' => $mascotaRepository->findBy([], ['nombre' => 'ASC']),
            'modal' => null,
        ]);
    }

    #[Route('/new', name: 'app_mascota_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MascotaRepository $mascotaRepository, UserRepository $userRepository): Response
    {
        $mascotum = new Mascota();
        $cliente = $userRepository->find($request->query->getInt('clienteId'));
        if ($cliente && in_array('ROLE_CLIENTE', $cliente->getRoles(), true)) {
            $mascotum->setCliente($cliente);
        }
        $form = $this->createForm(MascotaType::class, $mascotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mascotum->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($mascotum);
            $entityManager->flush();

            return $this->redirectToRoute('app_mascota_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mascota/index.html.twig', [
            'mascotas' => $mascotaRepository->findBy([], ['nombre' => 'ASC']),
            'modal' => [
                'title' => 'Nueva mascota',
                'description' => 'Añade los datos de la mascota y su cliente.',
                'closeUrl' => $this->generateUrl('app_mascota_index'),
                'formTemplate' => 'admin/mascota/_form.html.twig',
                'form' => $form->createView(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_mascota_show', methods: ['GET'])]
    public function show(Mascota $mascotum, CitaRepository $citaRepository): Response
    {
        return $this->render('admin/mascota/show.html.twig', [
            'mascotum' => $mascotum,
            'citas' => $citaRepository->findBy(['mascota' => $mascotum], ['inicio' => 'DESC']),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_mascota_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mascota $mascotum, EntityManagerInterface $entityManager, MascotaRepository $mascotaRepository): Response
    {
        $form = $this->createForm(MascotaType::class, $mascotum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mascotum->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_mascota_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/mascota/index.html.twig', [
            'mascotas' => $mascotaRepository->findBy([], ['nombre' => 'ASC']),
            'modal' => [
                'title' => 'Editar mascota',
                'description' => 'Actualiza la información de esta mascota.',
                'closeUrl' => $this->generateUrl('app_mascota_index'),
                'formTemplate' => 'admin/mascota/_form.html.twig',
                'form' => $form->createView(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_mascota_delete', methods: ['POST'])]
    public function delete(Request $request, Mascota $mascotum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mascotum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mascotum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mascota_index', [], Response::HTTP_SEE_OTHER);
    }
}

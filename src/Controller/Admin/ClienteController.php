<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\ClienteType;
use App\Repository\CitaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/cliente')]
final class ClienteController extends AbstractController
{
    #[Route(name: 'app_cliente_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        return $this->renderIndex($userRepository, estado: $this->normalizeEstado($request->query->get('estado')));
    }

    #[Route('/new', name: 'app_cliente_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $estado = $this->normalizeEstado($request->query->get('estado'));
        $cliente = new User();
        $form = $this->createForm(ClienteType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cliente
                ->setRoles(['ROLE_CLIENTE'])
                ->setCreatedAt(new \DateTimeImmutable());
            $cliente->setPassword($passwordHasher->hashPassword($cliente, $form->get('plainPassword')->getData()));
            $entityManager->persist($cliente);
            $entityManager->flush();

            return $this->redirectToRoute('app_cliente_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($userRepository, [
            'title' => 'Nuevo cliente',
            'description' => 'Registra los datos de contacto y acceso del cliente.',
            'form' => $form->createView(),
            'submitLabel' => 'Guardar cliente',
        ], $estado);
    }

    #[Route('/{id}/edit', name: 'app_cliente_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $cliente,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $this->denyUnlessClient($cliente);
        $estado = $this->normalizeEstado($request->query->get('estado'));
        $form = $this->createForm(ClienteType::class, $cliente, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $cliente->setPassword($passwordHasher->hashPassword($cliente, $plainPassword));
            }
            $cliente->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_cliente_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($userRepository, [
            'title' => 'Editar cliente',
            'description' => 'Actualiza los datos de contacto de este cliente.',
            'form' => $form->createView(),
            'submitLabel' => 'Guardar cambios',
        ], $estado);
    }

    #[Route('/{id}', name: 'app_cliente_show', methods: ['GET'])]
    public function show(User $cliente, CitaRepository $citaRepository): Response
    {
        $this->denyUnlessClient($cliente);

        return $this->render('admin/cliente/show.html.twig', [
            'cliente' => $cliente,
            'citas' => $citaRepository->findForCliente($cliente),
        ]);
    }

    #[Route('/{id}/estado', name: 'app_cliente_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, User $cliente, EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessClient($cliente);
        $estado = $this->normalizeEstado($request->query->get('estado'));

        if (!$this->isCsrfTokenValid('toggle-cliente-'.$cliente->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'No se pudo validar la acción. Vuelve a intentarlo.');

            return $this->redirectToRoute('app_cliente_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        $cliente->setActivo(!$cliente->isActivo());
        $cliente->setUpdatedAt(new \DateTime());
        $entityManager->flush();
        $this->addFlash('success', $cliente->isActivo()
            ? sprintf('Se ha reactivado a %s.', $cliente->getNombre())
            : sprintf('Se ha suspendido a %s.', $cliente->getNombre()));

        return $this->redirectToRoute('app_cliente_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
    }

    private function renderIndex(UserRepository $userRepository, ?array $modal = null, string $estado = 'activos'): Response
    {
        $todosLosClientes = array_values(array_filter(
            $userRepository->findAll(),
            static fn (User $user): bool => in_array('ROLE_CLIENTE', $user->getRoles(), true),
        ));
        usort($todosLosClientes, static fn (User $left, User $right): int => strcasecmp($left->getApellido().' '.$left->getNombre(), $right->getApellido().' '.$right->getNombre()));

        $estadisticas = [
            'total' => count($todosLosClientes),
            'activos' => count(array_filter($todosLosClientes, static fn (User $cliente): bool => $cliente->isActivo())),
            'suspendidos' => count(array_filter($todosLosClientes, static fn (User $cliente): bool => !$cliente->isActivo())),
            'conMascotas' => count(array_filter($todosLosClientes, static fn (User $cliente): bool => !$cliente->getMascotas()->isEmpty())),
        ];
        $clientes = $todosLosClientes;

        return $this->render('admin/cliente/index.html.twig', [
            'clientes' => $clientes,
            'estadisticas' => $estadisticas,
            'estadoFiltro' => $estado,
            'modal' => $modal ? $modal + [
                'closeUrl' => $this->generateUrl('app_cliente_index', ['estado' => $estado]),
                'formTemplate' => 'admin/cliente/_form.html.twig',
            ] : null,
        ]);
    }

    private function normalizeEstado(?string $estado): string
    {
        return in_array($estado, ['todos', 'activos', 'suspendidos'], true) ? $estado : 'activos';
    }

    private function denyUnlessClient(User $user): void
    {
        if (!in_array('ROLE_CLIENTE', $user->getRoles(), true)) {
            throw $this->createNotFoundException();
        }
    }
}
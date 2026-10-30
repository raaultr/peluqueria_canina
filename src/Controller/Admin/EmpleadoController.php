<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\EmpleadoType;
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
#[Route('/admin/empleado')]
final class EmpleadoController extends AbstractController
{
    #[Route(name: 'app_empleado_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        return $this->renderIndex($userRepository, estado: $this->normalizeEstado($request->query->get('estado')));
    }

    #[Route('/new', name: 'app_empleado_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $estado = $this->normalizeEstado($request->query->get('estado'));
        $empleado = new User();
        $form = $this->createForm(EmpleadoType::class, $empleado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $empleado
                ->setRoles(['ROLE_EMPLEADO'])
                ->setCreatedAt(new \DateTimeImmutable());
            $empleado->setPassword($passwordHasher->hashPassword($empleado, $form->get('plainPassword')->getData()));
            $entityManager->persist($empleado);
            $entityManager->flush();

            return $this->redirectToRoute('app_empleado_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($userRepository, [
            'title' => 'Nuevo empleado',
            'description' => 'Registra los datos de contacto y acceso del empleado.',
            'form' => $form->createView(),
            'submitLabel' => 'Guardar empleado',
        ], $estado);
    }

    #[Route('/{id}/edit', name: 'app_empleado_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $empleado,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $this->denyUnlessEmployee($empleado);
        $estado = $this->normalizeEstado($request->query->get('estado'));
        $form = $this->createForm(EmpleadoType::class, $empleado, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $empleado->setPassword($passwordHasher->hashPassword($empleado, $plainPassword));
            }
            $empleado->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('app_empleado_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        return $this->renderIndex($userRepository, [
            'title' => 'Editar empleado',
            'description' => 'Actualiza los datos de contacto de este empleado.',
            'form' => $form->createView(),
            'submitLabel' => 'Guardar cambios',
        ], $estado);
    }

    #[Route('/{id}', name: 'app_empleado_show', methods: ['GET'])]
    public function show(User $empleado, CitaRepository $citaRepository): Response
    {
        $this->denyUnlessEmployee($empleado);

        return $this->render('admin/empleado/show.html.twig', [
            'empleado' => $empleado,
            'citas' => $citaRepository->findForEmpleado($empleado),
        ]);
    }

    #[Route('/{id}/estado', name: 'app_empleado_toggle_status', methods: ['POST'])]
    public function toggleStatus(Request $request, User $empleado, EntityManagerInterface $entityManager): Response
    {
        $this->denyUnlessEmployee($empleado);
        $estado = $this->normalizeEstado($request->query->get('estado'));

        if (!$this->isCsrfTokenValid('toggle-empleado-'.$empleado->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('error', 'No se pudo validar la acción. Vuelve a intentarlo.');

            return $this->redirectToRoute('app_empleado_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
        }

        $empleado->setActivo(!$empleado->isActivo());
        $empleado->setUpdatedAt(new \DateTime());
        $entityManager->flush();
        $this->addFlash('success', $empleado->isActivo()
            ? sprintf('Se ha reactivado a %s.', $empleado->getNombre())
            : sprintf('Se ha suspendido a %s.', $empleado->getNombre()));

        return $this->redirectToRoute('app_empleado_index', ['estado' => $estado], Response::HTTP_SEE_OTHER);
    }

    private function renderIndex(UserRepository $userRepository, ?array $modal = null, string $estado = 'activos'): Response
    {
        $empleados = array_values(array_filter(
            $userRepository->findAll(),
            static fn (User $user): bool => in_array('ROLE_EMPLEADO', $user->getRoles(), true),
        ));
        usort($empleados, static fn (User $left, User $right): int => strcasecmp($left->getApellido().' '.$left->getNombre(), $right->getApellido().' '.$right->getNombre()));

        $estadisticas = [
            'total' => count($empleados),
            'activos' => count(array_filter($empleados, static fn (User $empleado): bool => $empleado->isActivo())),
            'suspendidos' => count(array_filter($empleados, static fn (User $empleado): bool => !$empleado->isActivo())),
            'conCitas' => count(array_filter($empleados, static fn (User $empleado): bool => !$empleado->getCitasComoTrabajador()->isEmpty())),
        ];

        return $this->render('admin/empleado/index.html.twig', [
            'empleados' => $empleados,
            'estadisticas' => $estadisticas,
            'estadoFiltro' => $estado,
            'modal' => $modal ? $modal + [
                'closeUrl' => $this->generateUrl('app_empleado_index', ['estado' => $estado]),
                'formTemplate' => 'admin/empleado/_form.html.twig',
            ] : null,
        ]);
    }

    private function normalizeEstado(?string $estado): string
    {
        return in_array($estado, ['todos', 'activos', 'suspendidos'], true) ? $estado : 'activos';
    }

    private function denyUnlessEmployee(User $user): void
    {
        if (!in_array('ROLE_EMPLEADO', $user->getRoles(), true)) {
            throw $this->createNotFoundException();
        }
    }
}
<?php

namespace App\Repository;

use App\Entity\Cita;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cita>
 */
class CitaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cita::class);
    }

    /** @return Cita[] */
    public function findForCliente(User $cliente): array
    {
        return $this->createQueryBuilder('cita')
            ->innerJoin('cita.mascota', 'mascota')
            ->addSelect('mascota')
            ->leftJoin('cita.citaServicios', 'citaServicio')
            ->addSelect('citaServicio')
            ->leftJoin('citaServicio.servicio', 'servicio')
            ->addSelect('servicio')
            ->andWhere('mascota.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('cita.inicio', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Cita[] */
    public function findForEmpleado(User $empleado): array
    {
        return $this->createQueryBuilder('cita')
            ->innerJoin('cita.mascota', 'mascota')
            ->addSelect('mascota')
            ->leftJoin('cita.citaServicios', 'citaServicio')
            ->addSelect('citaServicio')
            ->leftJoin('citaServicio.servicio', 'servicio')
            ->addSelect('servicio')
            ->andWhere('cita.trabajador = :empleado')
            ->setParameter('empleado', $empleado)
            ->orderBy('cita.inicio', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Cita[] Returns an array of Cita objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cita
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

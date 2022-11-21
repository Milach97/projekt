<?php

namespace App\Repository\Medicament;

use App\Entity\Medicament\MedicamentSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicamentSchedule>
 *
 * @method MedicamentSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicamentSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicamentSchedule[]    findAll()
 * @method MedicamentSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicamentScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicamentSchedule::class);
    }

    public function save(MedicamentSchedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicamentSchedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MedicamentSchedule[] Returns an array of MedicamentSchedule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MedicamentSchedule
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    public function add(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Users $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Users[] Returns an array of Users objects
//     */
   public function findUsersByAgeInterval($min, $max): array
   {
      $qb = $this->createQueryBuilder('u');
      $this->addInterval($qb, $min, $max);
      
      return  $qb
           ->getQuery()
           ->getResult()
       ;
   }

   public function statUsersByAgeInterval($min, $max): array
   {
        $qb = $this->createQueryBuilder('u')
            ->select('avg(u.age) as averageAge, count(u.id) as numberOfUsers');
        $this->addInterval($qb,$min,$max);
           
        return $qb
            ->getQuery()
            ->getResult()
       ;
   }

   private function addInterval(QueryBuilder $qb, $min, $max)
   {
        $qb
        ->andWhere('u.age >= :min and u.age <= :max')
        ->setParameters(['min' => $min, 'max' => $max])
        ->orderBy('u.age', 'ASC')
        ;
   }

//    public function findOneBySomeField($value): ?Users
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

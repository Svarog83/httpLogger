<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
	private int $maxRowsPerPage;

	public function __construct( ManagerRegistry $registry, int $maxRowsPerPage)
    {
        parent::__construct($registry, Log::class);
		$this->maxRowsPerPage = $maxRowsPerPage;
	}

	/**
	 * @param int $offset
	 * @param ?int $filterByIP
	 * @return Paginator
	 */
	public function getLogsPaginator(int $offset, ?int $filterByIP = 0): Paginator {
		$qb = $this->createQueryBuilder( 'l' )
					  ->orderBy( 'l.id', 'DESC' )
					  ->setMaxResults( $this->maxRowsPerPage )
					  ->setFirstResult( $offset );

		if ($filterByIP !== null) {
			$qb->andWhere( 'l.ip = :ip' )->setParameter( 'ip', $filterByIP );
		}

		$query = $qb->getQuery();

		return new Paginator( $query );
	}

	/**
	 * Returns number of rows in logs table
	 *
	 * @return int|mixed|string
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countRows() {
		return $this->createQueryBuilder( 'l' )->select( 'count(l.id) as count' )->getQuery()->getSingleScalarResult();
	}

	/**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Log $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Log $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return Log[] Returns an array of Log objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Log
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

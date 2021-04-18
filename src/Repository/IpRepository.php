<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip[]    findAll()
 * @method Ip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IpRepository extends ServiceEntityRepository
{
    /**
     * IpRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip::class);
    }

    /**
     * search
     *
     * @param array $filters
     *
     * @return Query
     */
    public function search(array $filters): Query
    {
        $filters = array_merge([
            'start' => 0,
            'length' => 10,
            'search' => '',
            'order' => [],
        ], $filters);

        $alias = 'i';
        $queryBuilder = $this->createQueryBuilder($alias);

        if (!empty($filters['search'])) {
            $expBuilder = $queryBuilder->expr();
            $queryBuilder->setParameter('search', sprintf('%%%s%%', $filters['search']));
            $queryBuilder->leftJoin('i.dnsRecords', 'dr');
            $queryBuilder->leftJoin('dr.record', 'r');
            $queryBuilder->leftJoin('dr.name', 'n');
            $queryBuilder->where(
                $expBuilder->orX(
                    $expBuilder->like('i.name', ':search'),
                    $expBuilder->like('i.type', ':search'),
                    $expBuilder->like('i.category', ':search'),
                    $expBuilder->like('i.route', ':search'),
                    $expBuilder->like('i.registry', ':search'),
                    $expBuilder->like('i.organization', ':search'),
                    $expBuilder->like('i.country', ':search'),
                    $expBuilder->like('i.asn', ':search'),
                    $expBuilder->like('r.name', ':search'),
                    $expBuilder->like('n.name', ':search'),
                )
            );
        }

        if (\count($filters['order'])) {
            foreach ($filters['order'] as [ $sort, $order ]) {
                $queryBuilder->addOrderBy(sprintf($sort, $alias), $order);
            }
        }

        return $queryBuilder->getQuery();
    }
}

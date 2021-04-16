<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DomainManager
 */
class DomainManager
{
    private DomainRepository $domainRepository;
    private EntityManagerInterface $entityManager;

    /**
     * DomainManager constructor.
     *
     * @param DomainRepository       $domainRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        DomainRepository $domainRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->domainRepository = $domainRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * create
     *
     * @param string $name
     *
     * @return Domain
     */
    public function create(string $name): Domain
    {
        $domain = $this->domainRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$domain) {
            $domain = new Domain();
            $domain->setName($name);
            $this->entityManager->persist($domain);
        }

        return $domain;
    }
}

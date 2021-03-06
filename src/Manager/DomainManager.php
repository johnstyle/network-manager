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
     * @return Domain|null
     */
    public function sync(string $name): ?Domain
    {
        if (
            filter_var($name, FILTER_VALIDATE_IP)
            || !preg_match('/^[[:alnum:]\-\.]+\.[[:alnum:]]+$/', $name)
        ) {
            return null;
        }

        $name = preg_replace(
            '/^(?:.+\.)?([[:alnum:]\-]+\.((?:(?:com?|net|gou?v|edu)\.)?[[:alnum:]]+))$/U',
            '$1',
            $name
        );

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

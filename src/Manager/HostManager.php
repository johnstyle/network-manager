<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Host;
use App\Repository\HostRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class HostManager
 */
class HostManager
{
    private HostRepository $hostRepository;
    private EntityManagerInterface $entityManager;

    /**
     * HostManager constructor.
     *
     * @param HostRepository         $hostRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        HostRepository $hostRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->hostRepository = $hostRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * create
     *
     * @param string $name
     *
     * @return Host
     */
    public function create(string $name): Host
    {
        $host = $this->hostRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$host) {
            $host = new Host();
            $host->setName($name);
            $this->entityManager->persist($host);
        }

        return $host;
    }
}

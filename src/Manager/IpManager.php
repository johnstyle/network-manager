<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Ip;
use App\Repository\IpRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class IpManager
 */
class IpManager
{
    private IpRepository $ipRepository;
    private EntityManagerInterface $entityManager;

    /**
     * IpManager constructor.
     *
     * @param IpRepository           $ipRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        IpRepository $ipRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->ipRepository = $ipRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * create
     *
     * @param string $name
     *
     * @return Ip
     */
    public function create(string $name): Ip
    {
        $ip = $this->ipRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$ip) {
            $ip = new Ip();
            $ip->setName($name);
            $this->entityManager->persist($ip);
        }

        return $ip;
    }
}

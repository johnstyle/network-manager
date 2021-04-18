<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\DnsRecord;
use App\Entity\Host;
use App\Entity\Ip;
use App\Repository\DnsRecordRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DnsRecordManager
 */
class DnsRecordManager
{
    private HostManager $hostManager;
    private DomainManager $domainManager;
    private DnsRecordRepository $dnsRecordRepository;
    private EntityManagerInterface $entityManager;

    /**
     * DnsRecordManager constructor.
     *
     * @param HostManager            $hostManager
     * @param DomainManager          $domainManager
     * @param DnsRecordRepository    $dnsRecordRepository
     * @param EntityManagerInterface $dnsRecordManager
     */
    public function __construct(
        HostManager $hostManager,
        DomainManager $domainManager,
        DnsRecordRepository $dnsRecordRepository,
        EntityManagerInterface $dnsRecordManager
    ) {

        $this->hostManager = $hostManager;
        $this->domainManager = $domainManager;
        $this->dnsRecordRepository = $dnsRecordRepository;
        $this->entityManager = $dnsRecordManager;
    }

    /**
     * create
     *
     * @param Ip     $ip
     * @param int    $ttl
     * @param string $class
     * @param string $type
     * @param string $name
     *
     * @return DnsRecord|null
     */
    public function sync(Ip $ip, int $ttl, string $class, string $type, string $name): ?DnsRecord
    {
        $host = $this->hostManager->sync($name);
        if (!$host) {
            return null;
        }

        $host->setDomain($this->domainManager->sync($name));

        $dnsRecord = $this->dnsRecordRepository->findOneBy([
            'name' => $host,
            'record' => $ip,
        ]);

        if (!$dnsRecord) {
            $dnsRecord = new DnsRecord();
            $dnsRecord->setName($host);
            $dnsRecord->setRecord($ip);
            $this->entityManager->persist($dnsRecord);
        }

        $dnsRecord->setTtl($ttl);
        $dnsRecord->setClass($class);
        $dnsRecord->setType($type);

        return $dnsRecord;
    }
}

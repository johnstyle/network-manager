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
    private DnsRecordRepository $dnsRecordRepository;
    private EntityManagerInterface $entityManager;

    /**
     * DnsRecordManager constructor.
     *
     * @param DnsRecordRepository   $dnsRecordRepository
     * @param EntityManagerInterface $dnsRecordManager
     */
    public function __construct(
        DnsRecordRepository $dnsRecordRepository,
        EntityManagerInterface $dnsRecordManager
    ) {
        $this->dnsRecordRepository = $dnsRecordRepository;
        $this->entityManager = $dnsRecordManager;
    }

    /**
     * create
     *
     * @param Host $name
     * @param Ip   $record
     *
     * @return DnsRecord
     */
    public function create(Host $name, Ip $record): DnsRecord
    {
        $dnsRecord = $this->dnsRecordRepository->findOneBy([
            'name' => $name,
            'record' => $record,
        ]);

        if (!$dnsRecord) {
            $dnsRecord = new DnsRecord();
            $dnsRecord->setName($name);
            $dnsRecord->setRecord($record);
            $this->entityManager->persist($dnsRecord);
        }

        return $dnsRecord;
    }
}

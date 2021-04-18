<?php

declare(strict_types=1);

namespace App\Command;

use App\Manager\DnsRecordManager;
use App\Manager\DomainManager;
use App\Manager\HostManager;
use App\Manager\IpManager;
use App\Service\Dig;
use App\Service\Whois;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class IpImportCommand
 */
class IpImportCommand extends Command
{
    protected static $defaultName = 'app:ip:import';

    private Dig $dig;
    private Whois $whois;
    private IpManager $ipManager;
    private HostManager $hostManager;
    private DomainManager $domainManager;
    private DnsRecordManager $dnsRecordManager;
    private EntityManagerInterface $entityManager;

    /**
     * IpImportCommand constructor.
     *
     * @param Dig                    $dig
     * @param Whois                  $whois
     * @param IpManager              $ipManager
     * @param HostManager            $hostManager
     * @param DomainManager          $domainManager
     * @param DnsRecordManager      $dnsRecordManager
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        Dig $dig,
        Whois $whois,
        IpManager $ipManager,
        HostManager $hostManager,
        DomainManager $domainManager,
        DnsRecordManager $dnsRecordManager,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->dig = $dig;
        $this->whois = $whois;
        $this->ipManager = $ipManager;
        $this->hostManager = $hostManager;
        $this->domainManager = $domainManager;
        $this->dnsRecordManager = $dnsRecordManager;
        $this->entityManager = $entityManager;
    }

    /**
     * configure
     */
    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('type', 't', InputOption::VALUE_REQUIRED)
            ->addOption('category', 'c', InputOption::VALUE_REQUIRED)
        ;
    }

    /**
     * execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $type = $input->getOption('type');
        $category = $input->getOption('category');

        if (!file_exists($file)) {
            $output->writeln(sprintf('File %s does not exists.', $file));
            return Command::FAILURE;
        }

        $propertyAccessor = new PropertyAccessor();

        foreach (file($file) as $name) {
            $name = trim($name);
            if (!$name || '#' === $name[0]) {
                continue;
            }

            $name = preg_replace('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\/\d+$/', '$1', $name);
            if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $name)) {
                continue;
            }

            $ip = $this->ipManager->create($name);

            $ip->setType($type);
            $ip->setCategory($category);

            if (!$ip->getWhoisSyncedAt()) {
                $whoisData = $this->whois->findFromCymru($name, [
                    'route' => [ 'cidr', 'route' ],
                    'registry' => [ 'source' ],
                    'country' => [ 'country' ],
                    'organization' => [ 'orgname', 'descr' ],
                    'asn' => [ 'originas', 'origin' ],
                    'allocatedAt' => [ 'allocated' ],
                ]);

                foreach ($whoisData as $whoisName => $whoisValue) {
                    $propertyAccessor->setValue($ip, $whoisName, $whoisValue);
                }

//                $ip->setWhoisSyncedAt(new \DateTime());
            }

            if (!$ip->getDigSyncedAt()) {
                $dnsReverse = null;
                $dnsRecords = new ArrayCollection();

                foreach ($this->dig->find($name) as $item) {
                    $dnsRecord = $this->dnsRecordManager->sync(
                        $ip,
                        $item['ttl'],
                        $item['class'],
                        $item['type'],
                        $item['record'],
                    );

                    if (!$dnsRecord) {
                        continue;
                    }

                    $dnsRecords->add($dnsRecord);

                    if (!$dnsReverse) {
                        $dnsReverse = $item['name'];
                    }
                }

                $ip->setReverse($dnsReverse);
                $ip->setDnsRecords($dnsRecords);
//                $ip->setDigSyncedAt(new \DateTime());
            }

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}

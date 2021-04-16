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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    /**
     * execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln(sprintf('File %s does not exists.', $file));
            return Command::FAILURE;
        }

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

            if (!$ip->getWhoisSyncedAt() && $this->whois->sendRequest($name)) {
                $ip->setRoute($this->whois->findValue([ 'cidr', 'route' ]));
                $ip->setSource($this->whois->findValue([ 'source' ]));
                $ip->setCountry($this->whois->findValue([ 'country' ]));
                $ip->setNetworkName($this->whois->findValue([ 'netname' ]));
                $ip->setNetworkHandle($this->whois->findValue([ 'nethandle', 'nichdl' ]));
                $ip->setOrganizationName($this->whois->findValue([ 'orgname', 'descr' ]));
                $ip->setOrganizationHandle($this->whois->findValue([ 'orgid', 'mntby' ]));
                $ip->setAsn($this->whois->findValue([ 'originas', 'origin' ]));
//                $ip->setWhoisSyncedAt(new \DateTime());
            }

            if (!$ip->getDigSyncedAt() && $this->dig->sendRequest($name)) {
                $dnsRecords = new ArrayCollection();
                $dnsReverse = null;
                foreach ($this->dig->getData() as $item) {
                    $host = $this->hostManager->create($item['record']);

                    $domainName = preg_replace(
                        '/^(?:.+\.)?([[:alnum:]\-]+\.((?:(?:com?|net|gou?v|edu)\.)?[[:alnum:]]+))$/U',
                        '$1',
                        $item['record']
                    );

                    $domain = $this->domainManager->create($domainName);
                    $host->setDomain($domain);

                    $dnsRecord = $this->dnsRecordManager->create($host, $ip);
                    $dnsRecord->setTtl($item['ttl']);
                    $dnsRecord->setClass($item['class']);
                    $dnsRecord->setType($item['type']);
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

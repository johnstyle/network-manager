<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidInterface;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidTrait;

/**
 * Class Host
 * @ORM\Entity(repositoryClass="App\Repository\HostRepository")
 */
class Host implements UuidInterface, TimestampableInterface
{
    use UuidTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Domain", inversedBy="hosts")
     */
    private ?Domain $domain;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DnsRecord", mappedBy="name", cascade={"persist"})
     */
    private Collection $dnsRecords;

    /**
     * Host constructor.
     */
    public function __construct()
    {
        $this->dnsRecords = new ArrayCollection();
    }

    /**
     * getName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getDomain
     *
     * @return Domain|null
     */
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    /**
     * setDomain
     *
     * @param Domain|null $domain
     *
     * @return $this
     */
    public function setDomain(?Domain $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * getDnsRecords
     *
     * @return ArrayCollection|Collection
     */
    public function getDnsRecords()
    {
        return $this->dnsRecords;
    }

    /**
     * addDnsRecord
     *
     * @param DnsRecord $dnsRecord
     *
     * @return $this
     */
    public function addDnsRecord(DnsRecord $dnsRecord): self
    {
        $dnsRecord->setHost($this);
        if (!$this->dnsRecords->contains($dnsRecord)) {
            $this->dnsRecords->add($dnsRecord);
        }

        return $this;
    }

    /**
     * setDnsRecords
     *
     * @param array|ArrayCollection|Collection $dnsRecords
     *
     * @return $this
     */
    public function setDnsRecords($dnsRecords): self
    {
        if (\is_array($dnsRecords)) {
            $this->dnsRecords = new ArrayCollection();
            foreach ($dnsRecords as $item) {
                $this->addDnsRecord($item);
            }
        } else {
            $this->dnsRecords = $dnsRecords;
        }

        return $this;
    }

    /**
     * removeDnsRecord
     *
     * @param DnsRecord $dnsRecord
     *
     * @return $this
     */
    public function removeDnsRecord(DnsRecord $dnsRecord): self
    {
        $this->dnsRecords->removeElement($dnsRecord);

        return $this;
    }
}

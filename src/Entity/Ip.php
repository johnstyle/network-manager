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
 * Class Ip
 * @ORM\Entity(repositoryClass="App\Repository\IpRepository")
 */
class Ip implements UuidInterface, TimestampableInterface
{
    use UuidTrait;
    use TimestampableTrait;

    public const TYPE_IPV4 = 'ipv4';
    public const TYPE_IPV6 = 'ipv6';

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $type = self::TYPE_IPV4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $reverse = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $route = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $source = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $networkName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $networkHandle = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $organizationName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $organizationHandle = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $asn = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DnsRecord", mappedBy="record", cascade={"persist"})
     */
    private Collection $dnsRecords;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $whoisSyncedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $digSyncedAt = null;

    /**
     * DomainRegistry constructor.
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
     * getType
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * setType
     *
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * getReverse
     *
     * @return string|null
     */
    public function getReverse(): ?string
    {
        return $this->reverse;
    }

    /**
     * setReverse
     *
     * @param string|null $reverse
     *
     * @return $this
     */
    public function setReverse(?string $reverse): self
    {
        $this->reverse = $reverse;

        return $this;
    }

    /**
     * getRoute
     *
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * setRoute
     *
     * @param string|null $route
     *
     * @return $this
     */
    public function setRoute(?string $route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * getSource
     *
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * setSource
     *
     * @param string|null $source
     *
     * @return $this
     */
    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * getCountry
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * setCountry
     *
     * @param string|null $country
     *
     * @return $this
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * getNetworkName
     *
     * @return string|null
     */
    public function getNetworkName(): ?string
    {
        return $this->networkName;
    }

    /**
     * setNetworkName
     *
     * @param string|null $networkName
     *
     * @return $this
     */
    public function setNetworkName(?string $networkName): self
    {
        $this->networkName = $networkName;

        return $this;
    }

    /**
     * getNetworkHandle
     *
     * @return string|null
     */
    public function getNetworkHandle(): ?string
    {
        return $this->networkHandle;
    }

    /**
     * setNetworkHandle
     *
     * @param string|null $networkHandle
     *
     * @return $this
     */
    public function setNetworkHandle(?string $networkHandle): self
    {
        $this->networkHandle = $networkHandle;

        return $this;
    }

    /**
     * getOrganizationName
     *
     * @return string|null
     */
    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    /**
     * setOrganizationName
     *
     * @param string|null $organizationName
     *
     * @return $this
     */
    public function setOrganizationName(?string $organizationName): self
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * getOrganizationHandle
     *
     * @return string|null
     */
    public function getOrganizationHandle(): ?string
    {
        return $this->organizationHandle;
    }

    /**
     * setOrganizationHandle
     *
     * @param string|null $organizationHandle
     *
     * @return $this
     */
    public function setOrganizationHandle(?string $organizationHandle): self
    {
        $this->organizationHandle = $organizationHandle;

        return $this;
    }

    /**
     * getAsn
     *
     * @return string|null
     */
    public function getAsn(): ?string
    {
        return $this->asn;
    }

    /**
     * setAsn
     *
     * @param string|null $asn
     *
     * @return $this
     */
    public function setAsn(?string $asn): self
    {
        $this->asn = $asn;

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
        $dnsRecord->setRecord($this);
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

    /**
     * getWhoisSyncedAt
     *
     * @return \DateTime|null
     */
    public function getWhoisSyncedAt(): ?\DateTime
    {
        return $this->whoisSyncedAt;
    }

    /**
     * setWhoisSyncedAt
     *
     * @param \DateTime|null $whoisSyncedAt
     *
     * @return $this
     */
    public function setWhoisSyncedAt(?\DateTime $whoisSyncedAt): self
    {
        $this->whoisSyncedAt = $whoisSyncedAt;

        return $this;
    }

    /**
     * getDigSyncedAt
     *
     * @return \DateTime|null
     */
    public function getDigSyncedAt(): ?\DateTime
    {
        return $this->digSyncedAt;
    }

    /**
     * setDigSyncedAt
     *
     * @param \DateTime|null $digSyncedAt
     *
     * @return $this
     */
    public function setDigSyncedAt(?\DateTime $digSyncedAt): self
    {
        $this->digSyncedAt = $digSyncedAt;

        return $this;
    }
}

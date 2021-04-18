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

    public const PROCOTOL_IPV4 = 'ipv4';
    public const PROCOTOL_IPV6 = 'ipv6';

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $protocol = self::PROCOTOL_IPV4;

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
    private ?string $registry = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $organization = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $asn = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $category = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DnsRecord", mappedBy="record", cascade={"persist"})
     */
    private Collection $dnsRecords;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $allocatedAt = null;

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
     * getProtocol
     *
     * @return string|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * setProtocol
     *
     * @param string|null $protocol
     *
     * @return $this
     */
    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;

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
     * getRegistry
     *
     * @return string|null
     */
    public function getRegistry(): ?string
    {
        return $this->registry;
    }

    /**
     * setRegistry
     *
     * @param string|null $registry
     *
     * @return $this
     */
    public function setRegistry(?string $registry): self
    {
        $this->registry = $registry;

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
     * getOrganization
     *
     * @return string|null
     */
    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    /**
     * setOrganization
     *
     * @param string|null $organization
     *
     * @return $this
     */
    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * getAsn
     *
     * @return int|null
     */
    public function getAsn(): ?int
    {
        return $this->asn;
    }

    /**
     * setAsn
     *
     * @param string|int|null $asn
     *
     * @return $this
     */
    public function setAsn($asn): self
    {
        if (null !== $asn && !\is_int($asn)) {
            $asn = (int) $asn;
            if (0 === $asn) {
                $asn = null;
            }
        }

        $this->asn = $asn;

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
     * getCategory
     *
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * setCategory
     *
     * @param string|null $category
     *
     * @return $this
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

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
        $dnsRecord->setIp($this);
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
     * getAllocatedAt
     *
     * @return \DateTime|null
     */
    public function getAllocatedAt(): ?\DateTime
    {
        return $this->allocatedAt;
    }

    /**
     * setAllocatedAt
     *
     * @param string|\DateTime|null $allocatedAt
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setAllocatedAt($allocatedAt): self
    {
        if (null !== $allocatedAt && !$allocatedAt instanceof \DateTime) {
            $allocatedAt = new \DateTime($allocatedAt);
        }

        $this->allocatedAt = $allocatedAt;

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

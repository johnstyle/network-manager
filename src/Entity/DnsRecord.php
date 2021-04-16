<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidInterface;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidTrait;

/**
 * Class DnsRecord
 * @ORM\Entity(repositoryClass="App\Repository\DnsRecordRepository")
 */
class DnsRecord implements UuidInterface, TimestampableInterface
{
    use UuidTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Host", inversedBy="dnsRecords")
     * @ORM\JoinColumn(name="name_id", referencedColumnName="id")
     */
    private ?Host $name;

    /**
     * @ORM\Column(type="integer")
     */
    private int $ttl = 0;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $class = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $type = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ip", inversedBy="dnsRecords")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     */
    private ?Ip $record;

    /**
     * getName
     *
     * @return Host|null
     */
    public function getName(): ?Host
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param Host|null $name
     *
     * @return $this
     */
    public function setName(?Host $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * getTtl
     *
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * setTtl
     *
     * @param int $ttl
     *
     * @return $this
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * getClass
     *
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * setClass
     *
     * @param string|null $class
     *
     * @return $this
     */
    public function setClass(?string $class): self
    {
        $this->class = $class;

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
     * getRecord
     *
     * @return Ip|null
     */
    public function getRecord(): ?Ip
    {
        return $this->record;
    }

    /**
     * setRecord
     *
     * @param Ip|null $record
     *
     * @return $this
     */
    public function setRecord(?Ip $record): self
    {
        $this->record = $record;

        return $this;
    }
}

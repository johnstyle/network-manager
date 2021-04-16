<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidInterface;
use PhpGuild\DoctrineExtraBundle\Model\Uuid\UuidTrait;

/**
 * Class Domain
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 */
class Domain implements UuidInterface, TimestampableInterface
{
    use UuidTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Host", mappedBy="domain", cascade={"persist"})
     */
    private Collection $hosts;

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
     * getHosts
     *
     * @return Collection
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    /**
     * setHosts
     *
     * @param Collection $hosts
     *
     * @return $this
     */
    public function setHosts(Collection $hosts): self
    {
        $this->hosts = $hosts;

        return $this;
    }
}

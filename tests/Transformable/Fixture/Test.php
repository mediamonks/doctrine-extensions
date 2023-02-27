<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Mediamonks\Doctrine\Tests\Transformable\Fixture;

use Doctrine\ORM\Mapping as ORM;
use MediaMonks\Doctrine\Mapping as MediaMonks;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */
#[ORM\Entity]
#[ORM\Table(name: 'tests')]
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @MediaMonks\Transformable(name="mocked")
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[MediaMonks\Transformable(name: 'mocked')]
    private ?string $value = null;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(type: 'boolean')]
    private bool $updated = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function isUpdated(): bool
    {
        return $this->updated;
    }

    public function setUpdated(bool $updated): void
    {
        $this->updated = $updated;
    }
}

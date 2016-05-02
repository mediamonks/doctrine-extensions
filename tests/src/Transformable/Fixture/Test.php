<?php

namespace Transformable\Fixture;

use Doctrine\ORM\Mapping as ORM;
use MediaMonks\Doctrine\Mapping\Annotation as MediaMonks;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @MediaMonks\Transformable(name="mocked")
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $updated = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Test
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return Test
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }
}

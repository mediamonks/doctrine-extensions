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
     * @ORM\Column(type="string", length=128)
     * @MediaMonks\Transformable(name="noop")
     */
    private $value;

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

}

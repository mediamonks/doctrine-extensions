<?php

namespace MediaMonks\Doctrine\Transformable\Transformer;

class RotateTransformer extends AbstractTransformer
{
    const MAX = 26;

    /**
     * @var int
     */
    protected $number = 13;

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        if(isset($options['number'])) {
            $this->number = $options['number'];
        }
        return $this;
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function transform($value)
    {
        return $this->stringRotate($value, $this->number);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return $this->stringRotate($value, self::MAX - $this->number);
    }

    /**
     * @param $value
     * @param int $number
     * @return string
     */
    protected function stringRotate($value, $number = 13) {
        static $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
        $number = (int)$number % 26;
        if (!$number) return $value;
        if ($number < 0) $number += 26;
        if ($number == 13) return str_rot13($value);
        $rep = substr($letters, $number * 2) . substr($letters, 0, $number * 2);
        return strtr($value, $letters, $rep);
    }
}
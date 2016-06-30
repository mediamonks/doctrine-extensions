<?php

namespace MediaMonks\Doctrine\Transformable\Mapping\Driver;

use Gedmo\Mapping\Driver\File;
use Gedmo\Mapping\Driver;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\SoftDeleteable\Mapping\Validator;

/**
 * @author Oscar van Ruiten <oscarvanruiten@msn.com>
 */
class Yaml extends File implements Driver
{
    /**
     * File extension
     * @var string
     */
    protected $_extension = '.yml';

    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        $mapping = $this->_getMapping($meta->name);
        if (isset($mapping['fields'])) {
            foreach ($mapping['fields'] as $property => $propertyMapping) {
                if (isset($propertyMapping['mediamonks']['transformable']['name'])) {
                    $name = $propertyMapping['mediamonks']['transformable']['name'];
                    $config['transformable'][] = $this->getConfig($property, $name);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function _loadMappingFile($file)
    {
        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($file));
    }

    /**
     * @param $property
     * @param $name
     * @return array
     */
    protected function getConfig($property, $name)
    {
        return [
            'field' => $property,
            'name'  => $name
        ];
    }
}

<?php

namespace MediaMonks\Doctrine\Transformable\Mapping\Driver;

use Gedmo\Mapping\Driver;
use Gedmo\Mapping\Driver\File;

class Yaml extends File implements Driver
{
    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        $mapping = $this->_getMapping($meta->name);

        if (isset($mapping['fields'])) {
            foreach ($mapping['fields'] as $field => $fieldMapping) {
                if (isset($fieldMapping['mediamonks']['transformable'])) {
                    $mappingProperty = $field;

                    $config['transformable'][] = $this->getConfig($mappingProperty,
                        $fieldMapping['mediamonks']['transformable']['name']);
                }
            }
        }
    }

    /**
     * @param $property
     * @param $transformable
     * @return array
     */
    protected function getConfig($property, $transformable)
    {
        return [
            'field' => $property,
            'name'  => $transformable
        ];
    }

    /**
     * Loads a mapping file with the given name and returns a map
     * from class/entity names to their corresponding elements.
     *
     * @param string $file The mapping file to load.
     *
     * @return array
     */
    protected function _loadMappingFile($file)
    {
        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($file));
    }
}

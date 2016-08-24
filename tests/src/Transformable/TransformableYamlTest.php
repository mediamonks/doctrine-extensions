<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use MediaMonks\Doctrine\Transformable\Mapping\Driver\Yaml;
use Mockery as m;
use Tool\BaseTestCaseORM;

class TransformableYamlTest extends BaseTestCaseORM
{
    const ENTITY_TEST = "Transformable\\Fixture\\YamlTest";

    const VALUE = 'original';
    const VALUE_TRANSFORMED = 'transformed';

    const VALUE_2 = 'original_updated';
    const VALUE_2_TRANSFORMED = 'transformed_updated';

    public function testReadStructure()
    {
        $config = [];
        $meta   = $this->em->getClassMetadata('Transformable\Fixture\YamlTest');

        $driver = new Yaml();
        $driver->setLocator(new DefaultFileLocator([
            __DIR__ . '/Fixture',
        ], '.dcm.yml'));

        $driver->readExtendedMetadata($meta, $config);
    }

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY_TEST,
        ];
    }
}

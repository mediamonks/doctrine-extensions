<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Persistence\Mapping\Driver\DefaultFileLocator;
use MediaMonks\Doctrine\Transformable\Mapping\Driver\Yaml;
use Mockery as m;
use Tool\BaseTestCaseORM;

class TransformableYamlTest extends BaseTestCaseORM
{
    const ENTITY_TEST = "Transformable\\Fixture\\YamlTest";

    const VALUE = 'original';
    const VALUE_TRANSFORMED = 'transformed';

    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUpEntityManager($transformer = null)
    {
        $evm = new EventManager();
        $evm->addEventSubscriber($this->getSubscriber($transformer));

        $configuration = Setup::createYAMLMetadataConfiguration([
            __DIR__ . '/Fixture'
        ]);

        $this->em = $this->getMockSqliteEntityManager($evm, $configuration);
    }

    protected function getSubscriber($transformer = null)
    {
        if(is_null($transformer)) {
            $transformer = $this->getDefaultTransformer();
        }

        $transformerPool = m::mock('MediaMonks\Doctrine\Transformable\Transformer\TransformerPool');
        $transformerPool->shouldReceive('get')->andReturn($transformer);

        return new TransformableSubscriber($transformerPool);
    }

    /**
     * @return TransformableSubscriber
     */
    protected function getDefaultTransformer()
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer');
        $transformer->shouldReceive('transform')->andReturn(self::VALUE_TRANSFORMED);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE);
        return $transformer;
    }

    public function testReadStructure()
    {
        $this->setUpEntityManager();

        $config = [];
        $meta   = $this->em->getClassMetadata('Transformable\Fixture\YamlTest');

        $driver = new Yaml();
        $driver->setLocator(new DefaultFileLocator([
            __DIR__ . '/Fixture',
        ], '.dcm.yml'));

        $driver->readExtendedMetadata($meta, $config);

        $this->assertArrayHasKey('transformable', $config);
        $this->assertArrayHasKey(0, $config['transformable']);
        $this->assertArrayHasKey('field', $config['transformable'][0]);
        $this->assertArrayHasKey('name', $config['transformable'][0]);
        $this->assertEquals('value', $config['transformable'][0]['field']);
        $this->assertEquals('mocked', $config['transformable'][0]['name']);
    }

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY_TEST,
        ];
    }
}

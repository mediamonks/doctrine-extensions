<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Tool\BaseTestCaseORM;
use Transformable\Fixture\Test;
use \Mockery as m;

class TransformableTest extends BaseTestCaseORM
{
    const ENTITY_TEST = "Transformable\\Fixture\\Test";

    const VALUE = 'original';
    const VALUE_TRANSFORMED = 'transformed';

    const VALUE_2 = 'original_updated';
    const VALUE_2_TRANSFORMED = 'transformed_updated';

    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp()
    {
        $evm = new EventManager();
        $evm->addEventSubscriber($this->getSubscriber());
        $this->em = $this->getMockSqliteEntityManager($evm);
    }

    /**
     * @return TransformableSubscriber
     */
    protected function getSubscriber()
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer');
        $transformer->shouldReceive('transform')->andReturn(self::VALUE_TRANSFORMED, self::VALUE_2_TRANSFORMED);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE, self::VALUE_2);

        $transformerPool = m::mock('MediaMonks\Doctrine\Transformable\Transformer\TransformerPool');
        $transformerPool->shouldReceive('get')->andReturn($transformer);

        return new TransformableSubscriber($transformerPool);
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents = $this->getSubscriber()->getSubscribedEvents();

        $this->assertContains(Events::loadClassMetadata, $subscribedEvents);
        $this->assertContains(Events::onFlush, $subscribedEvents);
        $this->assertContains(Events::postPersist, $subscribedEvents);
        $this->assertContains(Events::postLoad, $subscribedEvents);
        $this->assertContains(Events::postUpdate, $subscribedEvents);
    }

    public function testTransformedValueIsStored()
    {
        $test = new Test();
        $test->setValue(self::VALUE);

        $this->em->persist($test);
        $this->em->flush();

        $dbRow = $this->em->getConnection()->fetchAssoc('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testTransformedValueIsStoredAfterChange()
    {
        $test = new Test();
        $test->setValue(self::VALUE);

        $this->em->persist($test);
        $this->em->flush();

        $test->setValue(self::VALUE_2);
        $this->em->flush();

        $dbRow = $this->em->getConnection()->fetchAssoc('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_2_TRANSFORMED, $dbRow['value']);
    }

    public function testReverseTransformOfAlreadyPresentValue()
    {
        $this->em->getConnection()->insert('tests', ['id' => 1, 'value' => self::VALUE_TRANSFORMED]);

        $test = $this->em->find('Transformable\Fixture\Test', 1);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY_TEST,
        ];
    }
}

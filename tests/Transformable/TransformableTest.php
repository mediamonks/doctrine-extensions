<?php

/** @noinspection SqlNoDataSourceInspection */

namespace MediaMonks\Doctrine\Tests\Transformable;

use Doctrine\Common\EventManager;
use Doctrine\ORM\Events;
use MediaMonks\Doctrine\Tests\Tool\BaseTestCaseORM;
use Mediamonks\Doctrine\Tests\Transformable\Fixture\Test;
use MediaMonks\Doctrine\Transformable\TransformableSubscriber;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;
use Mockery as m;

class TransformableTest extends BaseTestCaseORM
{
    const VALUE = 'original';
    const VALUE_TRANSFORMED = 'transformed';

    const VALUE_2 = 'original_updated';
    const VALUE_2_TRANSFORMED = 'transformed_updated';

    protected function setUpEntityManager($transformer = null, bool $annotations = false)
    {
        $evm = new EventManager();
        $evm->addEventSubscriber($this->getSubscriber($transformer));
        $this->em = $this->getDefaultMockSqliteEntityManager($evm, $annotations);
    }

    protected function getSubscriber($transformer = null): TransformableSubscriber
    {
        if (is_null($transformer)) {
            $transformer = $this->getDefaultTransformer();
        }

        $transformerPool = m::mock(TransformerPool::class);
        $transformerPool->shouldReceive('get')->andReturn($transformer);

        return new TransformableSubscriber($transformerPool);
    }

    protected function getDefaultTransformer(): TransformerInterface
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer', TransformerInterface::class);
        $transformer->shouldReceive('transform')->andReturn(self::VALUE_TRANSFORMED);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE);

        return $transformer;
    }

    public function testGetSubscribedEvents(): void
    {
        $this->setUpEntityManager();

        $subscribedEvents = $this->getSubscriber()->getSubscribedEvents();

        $this->assertContains(Events::loadClassMetadata, $subscribedEvents);
        $this->assertContains(Events::onFlush, $subscribedEvents);
        $this->assertContains(Events::postPersist, $subscribedEvents);
        $this->assertContains(Events::postLoad, $subscribedEvents);
        $this->assertContains(Events::postUpdate, $subscribedEvents);
    }

    public function testTransformedValueIsStored(): void
    {
        $this->setUpEntityManager();

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());

        $this->clear();

        $test = $this->find(Test::class, 1);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testAnnotationTransformedValueIsStored(): void
    {
        $this->setUpEntityManager(null, true);

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());

        $this->clear();

        $test = $this->find(Test::class, 1);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testSupportsNull(): void
    {
        $this->setUpEntityManager();

        $test = new Test();

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(null, $dbRow['value']);
        $this->assertNull($test->getValue());
    }

    public function testAnnotationSupportsNull(): void
    {
        $this->setUpEntityManager(null, true);

        $test = new Test();

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(null, $dbRow['value']);
        $this->assertNull($test->getValue());
    }

    public function testTransformAfterUpdate(): void
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer', TransformerInterface::class);
        $transformer->shouldReceive('transform')->andReturn(self::VALUE_TRANSFORMED, self::VALUE_2_TRANSFORMED);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE, self::VALUE_2);

        $this->setUpEntityManager($transformer);

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $test->setValue(self::VALUE_2);
        $this->flush();

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_2_TRANSFORMED, $dbRow['value']);
    }

    public function testAnnotationTransformAfterUpdate(): void
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer', TransformerInterface::class);
        $transformer->shouldReceive('transform')->andReturn(self::VALUE_TRANSFORMED, self::VALUE_2_TRANSFORMED);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE, self::VALUE_2);

        $this->setUpEntityManager($transformer, true);

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $test->setValue(self::VALUE_2);
        $this->flush();

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);

        $this->assertEquals(self::VALUE_2_TRANSFORMED, $dbRow['value']);
    }

    public function testReverseTransformOfAlreadyPresentValue(): void
    {
        $this->setUpEntityManager();

        $this->insert('tests', ['id' => 1, 'value' => self::VALUE_TRANSFORMED, 'updated' => 0]);

        $test = $this->find(Test::class, 1);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testAnnotationReverseTransformOfAlreadyPresentValue(): void
    {
        $this->setUpEntityManager(null, true);

        $this->insert('tests', ['id' => 1, 'value' => self::VALUE_TRANSFORMED, 'updated' => 0]);

        $test = $this->find(Test::class, 1);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testNotTransformingAnUnchangedValueTwice(): void
    {
        $this->setUpEntityManager();

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);
        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());

        $test->setUpdated(true);
        $this->flush();

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);
        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    public function testAnnotationNotTransformingAnUnchangedValueTwice(): void
    {
        $this->setUpEntityManager(null, true);

        $test = new Test();
        $test->setValue(self::VALUE);

        $this->persistAndFlush($test);

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);
        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());

        $test->setUpdated(true);
        $this->flush();

        $dbRow = $this->fetchAssociative('SELECT * FROM tests WHERE id = ?', [$test->getId()]);
        $this->assertEquals(self::VALUE_TRANSFORMED, $dbRow['value']);
        $this->assertEquals(self::VALUE, $test->getValue());
    }

    protected function getUsedEntityFixtures(): array
    {
        return [
            Test::class,
        ];
    }
}

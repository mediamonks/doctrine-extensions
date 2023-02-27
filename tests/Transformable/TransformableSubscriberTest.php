<?php

namespace MediaMonks\Doctrine\Tests\Transformable;

use Doctrine\ORM\Events;
use MediaMonks\Doctrine\Transformable\TransformableSubscriber;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class TransformableSubscriberTest extends TestCase
{
    const VALUE = 'foobar';
    protected TransformableSubscriber $transformableSubscriber;

    public function setUp(): void
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer', TransformerInterface::class);
        $transformer->shouldReceive('transform')->andReturn(self::VALUE);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE);

        $transformerPool = m::mock('MediaMonks\Doctrine\Transformable\Transformer\TransformerPool');
        $transformerPool->shouldReceive('get')->andReturn($transformer);

        $this->transformableSubscriber = new TransformableSubscriber($transformerPool);
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = $this->transformableSubscriber->getSubscribedEvents();

        $this->assertContains(Events::onFlush, $subscribedEvents);
        $this->assertContains(Events::postPersist, $subscribedEvents);
        $this->assertContains(Events::postLoad, $subscribedEvents);
        $this->assertContains(Events::postUpdate, $subscribedEvents);
    }

}

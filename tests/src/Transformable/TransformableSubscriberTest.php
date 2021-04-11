<?php

namespace MediaMonks\Doctrine\Transformable;

use \Mockery as m;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;

class TransformableSuscriberTest extends TestCase
{
    const VALUE = 'foobar';

    /**
     * @var TransformableSubscriber
     */
    protected $transformableSubscriber;

    public function setUp(): void
    {
        $transformer = m::mock('MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer');
        $transformer->shouldReceive('transform')->andReturn(self::VALUE);
        $transformer->shouldReceive('reverseTransform')->andReturn(self::VALUE);

        $transformerPool = m::mock('MediaMonks\Doctrine\Transformable\Transformer\TransformerPool');
        $transformerPool->shouldReceive('get')->andReturn($transformer);

        $this->transformableSubscriber = new TransformableSubscriber($transformerPool);
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents = $this->transformableSubscriber->getSubscribedEvents();

        $this->assertContains(Events::onFlush, $subscribedEvents);
        $this->assertContains(Events::postPersist, $subscribedEvents);
        $this->assertContains(Events::postLoad, $subscribedEvents);
        $this->assertContains(Events::postUpdate, $subscribedEvents);
    }

}

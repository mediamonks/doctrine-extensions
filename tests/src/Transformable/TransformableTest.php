<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventManager;
use MediaMonks\Doctrine\Transformable\Transformer\NoopTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;
use Tool\BaseTestCaseORM;
use Transformable\Fixture\User;
use \Mockery as m;

class TransformableTest extends BaseTestCaseORM
{
    const USER = "Transformable\\Fixture\\User";

    const EMAIL = 'robert@mediamonks.com';

    protected function setUp()
    {
        $transformer = new NoopTransformer();

        $transformerPool = new TransformerPool();
        $transformerPool->set('noop', $transformer);

        $evm = new EventManager();
        $evm->addEventSubscriber(new TransformableSubscriber($transformerPool));
        $this->getMockSqliteEntityManager($evm);
    }

    public function testValueRemainsEqualAfterFlush()
    {
        $user = new User();
        $user->setEmail(self::EMAIL);

        $this->em->persist($user);
        $this->em->flush();

        $this->assertEquals(self::EMAIL, $user->getEmail());
    }

    protected function getUsedEntityFixtures()
    {
        return [
            self::USER,
        ];
    }

}

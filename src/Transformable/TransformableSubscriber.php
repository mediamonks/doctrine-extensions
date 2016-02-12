<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventArgs;
use Gedmo\Mapping\MappedEventSubscriber;
use MediaMonks\Doctrine\Transformable\Transformer\DebugTransformer;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 * @author Bas Bloembergen <basb@mediamonks.com>
 */
class TransformableSubscriber extends MappedEventSubscriber
{
    /**
     * @var TransformerPool
     */
    protected $transformerPool;

    /**
     * TransformableListener constructor.
     * @param TransformerPool $transformerPool
     */
    public function __construct(TransformerPool $transformerPool)
    {
        $this->transformerPool = $transformerPool;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'onFlush',
            'postPersist',
            'postLoad',
            'postUpdate',
        ];
    }

    /**
     * @param EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * @param EventArgs $args
     */
    public function postLoad(EventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * @param EventArgs $args
     */
    public function onFlush(EventArgs $args)
    {
        $ea  = $this->getEventAdapter($args);
        $om  = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            $meta   = $om->getClassMetadata(get_class($object));
            $config = $this->getConfiguration($om, $meta->name);

            if (isset($config['transformable']) && $config['transformable']) {
                foreach ($config['transformable'] as $column) {
                    $reflProp = $meta->getReflectionProperty($column['field']);
                    $oldValue = $reflProp->getValue($object);
                    $reflProp->setValue($object,
                        $this->getTransformer($column['name'], $column['options'])->transform($oldValue));
                }
                $ea->recomputeSingleObjectChangeSet($uow, $meta, $object);
            }
        }

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            $meta   = $om->getClassMetadata(get_class($object));
            $config = $this->getConfiguration($om, $meta->name);

            if (isset($config['transformable']) && $config['transformable']) {
                foreach ($config['transformable'] as $column) {
                    $reflProp = $meta->getReflectionProperty($column['field']);
                    $oldValue = $reflProp->getValue($object);
                    $reflProp->setValue($object,
                        $this->getTransformer($column['name'], $column['options'])->transform($oldValue));
                }
                $ea->recomputeSingleObjectChangeSet($uow, $meta, $object);
            }
        }
    }

    /**
     * @param EventArgs $args
     */
    public function postPersist(EventArgs $args)
    {
        $ea     = $this->getEventAdapter($args);
        $om     = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta   = $om->getClassMetadata(get_class($object));

        $config = $this->getConfiguration($om, $meta->name);
        if (isset($config['transformable']) && $config['transformable']) {
            foreach ($config['transformable'] as $column) {
                $reflProp = $meta->getReflectionProperty($column['field']);
                $oldValue = $reflProp->getValue($object);
                $reflProp->setValue($object,
                    $this->getTransformer($column['name'], $column['options'])->reverseTransform($oldValue));
            }
        }
    }

    /**
     * @param $name
     * @param array $options
     * @return TransformerInterface
     */
    protected function getTransformer($name, $options)
    {
        $transformer = $this->transformerPool->get($name);
        $transformer->setOptions($options);
        return $transformer;
    }

    /**
     * {@inheritDoc}
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}
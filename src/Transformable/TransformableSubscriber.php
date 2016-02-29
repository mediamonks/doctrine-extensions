<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Gedmo\Mapping\Event\Adapter\ORM;
use Gedmo\Mapping\MappedEventSubscriber;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 * @author Bas Bloembergen <basb@mediamonks.com>
 */
class TransformableSubscriber extends MappedEventSubscriber
{
    const TRANSFORMABLE = 'transformable';

    const FUNCTION_TRANSFORM = 'transform';
    const FUNCTION_REVERSE_TRANSFORM = 'reverseTransform';

    const TYPE_TRANSFORMED = 'transformed';
    const TYPE_PLAIN = 'plain';

    /**
     * @var TransformerPool
     */
    protected $transformerPool;

    /**
     * @var array
     */
    protected $entityData = [];

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
    public function onFlush(EventArgs $args)
    {
        $this->transform($args);
    }

    /**
     * @param EventArgs $args
     */
    public function postPersist(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @param EventArgs $args
     */
    public function postLoad(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @param EventArgs $args
     */
    public function postUpdate(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @param EventArgs $args
     */
    protected function transform(EventArgs $args)
    {
        $ea  = $this->getEventAdapter($args);
        $om  = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            $this->handle($ea, $om, $uow, $object, self::FUNCTION_TRANSFORM);
        }

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            $this->handle($ea, $om, $uow, $object, self::FUNCTION_TRANSFORM);
        }
    }

    /**
     * @param EventArgs $args
     */
    protected function reverseTransform(EventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();

        $this->handle($ea, $om, $om->getUnitOfWork(), $ea->getObject(), self::FUNCTION_REVERSE_TRANSFORM);
    }

    /**
     * @param ORM $ea
     * @param EntityManager $om
     * @param UnitOfWork $uow
     * @param object $entity
     * @param string $method
     */
    protected function handle(ORM $ea, EntityManager $om, UnitOfWork $uow, $entity, $method)
    {
        $meta   = $om->getClassMetadata(get_class($entity));
        $config = $this->getConfiguration($om, $meta->name);

        if (isset($config[self::TRANSFORMABLE]) && $config[self::TRANSFORMABLE]) {
            foreach ($config[self::TRANSFORMABLE] as $column) {
                $this->handleField($entity, $method, $column, $meta);
            }
            $ea->recomputeSingleObjectChangeSet($uow, $meta, $entity);
        }
    }

    /**
     * @param $entity
     * @param $method
     * @param $column
     * @param $meta
     */
    protected function handleField($entity, $method, $column, $meta)
    {
        $field = $column['field'];
        $oid = spl_object_hash($entity);

        $reflProp = $meta->getReflectionProperty($field);
        $oldValue = $reflProp->getValue($entity);

        if ($method === self::FUNCTION_TRANSFORM
            && $this->getOriginalPlainFieldValue($oid, $field) === $oldValue
        ) {
            $newValue = $this->getOriginalTransformedFieldValue($oid, $field);
        } else {
            $newValue = $this->getTransformer($column['name'])->$method($oldValue);
        }

        $reflProp->setValue($entity, $newValue);

        if ($method === self::FUNCTION_REVERSE_TRANSFORM) {
            $this->storeOriginalFieldData($oid, $field, $oldValue, $newValue);
        }
    }

    /**
     * @param $oid
     * @param $field
     * @return null
     */
    protected function getOriginalPlainFieldValue($oid, $field)
    {
        if(!isset($this->entityData[$oid][$field])) {
            return null;
        }
        return $this->entityData[$oid][$field][self::TYPE_PLAIN];
    }

    /**
     * @param $oid
     * @param $field
     * @return mixed
     */
    protected function getOriginalTransformedFieldValue($oid, $field)
    {
        if(!isset($this->entityData[$oid][$field])) {
            return null;
        }
        return $this->entityData[$oid][$field][self::TYPE_TRANSFORMED];
    }

    /**
     * @param $oid
     * @param $field
     * @param $transformed
     * @param $plain
     */
    protected function storeOriginalFieldData($oid, $field, $transformed, $plain)
    {
        $this->entityData[$oid][$field] = [
            self::TYPE_TRANSFORMED => $transformed,
            self::TYPE_PLAIN => $plain
        ];
    }

    /**
     * @param $name
     * @return TransformerInterface
     */
    protected function getTransformer($name)
    {
        return $this->transformerPool->get($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}

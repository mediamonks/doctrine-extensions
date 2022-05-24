<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Gedmo\Mapping\Event\AdapterInterface;
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

    protected array $entityFieldValues = [];

    public function __construct(protected TransformerPool $transformerPool)
    {
        parent::__construct();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::onFlush,
            Events::postPersist,
            Events::postLoad,
            Events::postUpdate,
        ];
    }

    public function loadClassMetadata(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $this->loadMetadataForObjectClass($ea->getObjectManager(), $eventArgs->getClassMetadata());
    }

    /**
     * @throws \Exception
     */
    public function onFlush(EventArgs $args)
    {
        $this->transform($args);
    }

    /**
     * @throws \Exception
     */
    public function postPersist(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @throws \Exception
     */
    public function postLoad(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @throws \Exception
     */
    public function postUpdate(EventArgs $args)
    {
        $this->reverseTransform($args);
    }

    /**
     * @throws \Exception
     */
    protected function transform(EventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            $this->handle($ea, $om, $uow, $object, self::FUNCTION_TRANSFORM);
        }

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            $this->handle($ea, $om, $uow, $object, self::FUNCTION_TRANSFORM);
        }
    }

    /**
     * @throws \Exception
     */
    protected function reverseTransform(EventArgs $args)
    {
        $ea = $this->getEventAdapter($args);
        $om = $ea->getObjectManager();

        $this->handle($ea, $om, $om->getUnitOfWork(), $ea->getObject(), self::FUNCTION_REVERSE_TRANSFORM);
    }

    /**
     * @throws \Exception
     */
    protected function handle(AdapterInterface $ea, EntityManagerInterface $om, UnitOfWork $uow, object $entity, string $method)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $om
         */
        $meta = $om->getClassMetadata(get_class($entity));
        $config = $this->getConfiguration($om, $meta->name);

        if (isset($config[self::TRANSFORMABLE]) && $config[self::TRANSFORMABLE]) {
            foreach ($config[self::TRANSFORMABLE] as $column) {
                $this->handleField($entity, $method, $column, $meta);
            }

            $ea->recomputeSingleObjectChangeSet($uow, $meta, $entity);
        }
    }

    /**
     * @throws \Exception
     */
    protected function handleField(object $entity, string $method, array $column, ClassMetadata $meta)
    {
        $field = $column['field'];
        $oid = spl_object_hash($entity);

        $reflProp = $meta->getReflectionProperty($field);
        $oldValue = $this->getEntityValue($reflProp, $entity);
        $newValue = $this->getNewValue($oid, $field, $column['name'], $method, $oldValue);
        $reflProp->setValue($entity, $newValue);

        if ($method === self::FUNCTION_REVERSE_TRANSFORM) {
            $this->storeOriginalFieldData($oid, $field, $oldValue, $newValue);
        }
    }

    protected function getEntityValue(\ReflectionProperty $reflProp, object $entity): string|null
    {
        $value = $reflProp->getValue($entity);

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        return $value;
    }

    /**
     * @throws \Exception
     */
    protected function getNewValue(string $oid, string $field, string $transformerName, string $method, mixed $value): mixed
    {
        if ($method === self::FUNCTION_TRANSFORM
            && $this->getEntityFieldValue($oid, $field, self::TYPE_PLAIN) === $value
        ) {
            return $this->getEntityFieldValue($oid, $field, self::TYPE_TRANSFORMED);
        }

        return $this->performTransformerOperation($transformerName, $method, $value);
    }

    /**
     * @throws \Exception
     */
    protected function performTransformerOperation(string $transformerName, string $method, mixed $oldValue): mixed
    {
        if (is_null($oldValue)) {
            return null;
        }

        return $this->getTransformer($transformerName)->$method($oldValue);
    }

    protected function getEntityFieldValue(string $oid, string $field, string $type): mixed
    {
        if (!isset($this->entityFieldValues[$oid][$field])) {
            return null;
        }
        return $this->entityFieldValues[$oid][$field][$type];
    }

    protected function storeOriginalFieldData(string $oid, string $field, mixed $transformed, mixed $plain)
    {
        $this->entityFieldValues[$oid][$field] = [
            self::TYPE_TRANSFORMED => $transformed,
            self::TYPE_PLAIN => $plain,
        ];
    }

    /**
     * @throws \Exception
     */
    protected function getTransformer(string $name): TransformerInterface
    {
        return $this->transformerPool->get($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}

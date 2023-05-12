<?php

namespace MediaMonks\Doctrine\Transformable;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Gedmo\Mapping\Event\AdapterInterface;
use Gedmo\Mapping\MappedEventSubscriber;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerInterface;
use MediaMonks\Doctrine\Transformable\Transformer\TransformerPool;
use ReflectionProperty;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 * @author Bas Bloembergen <basb@mediamonks.com>
 */
class TransformableSubscriber extends MappedEventSubscriber
{
    const TRANSFORMABLE = 'transformable';

    protected array $entityFieldValues = [];

    public function __construct(protected readonly TransformerPool $transformerPool)
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

    public function loadClassMetadata(EventArgs $eventArguments): void
    {
        $eventAdapter = $this->getEventAdapter($eventArguments);
        $this->loadMetadataForObjectClass($eventAdapter->getObjectManager(), $eventArguments->getClassMetadata());
    }

    /**
     * @throws Exception
     */
    public function onFlush(OnFlushEventArgs $eventArguments): void
    {
        $this->transform($eventArguments);
    }

    /**
     * @throws Exception
     */
    public function postPersist(PostPersistEventArgs $eventArguments): void
    {
        $this->reverseTransform($eventArguments, $eventArguments->getObject());
    }

    /**
     * @throws Exception
     */
    public function postLoad(PostLoadEventArgs $eventArguments): void
    {
        $this->reverseTransform($eventArguments, $eventArguments->getObject());
    }

    /**
     * @throws Exception
     */
    public function postUpdate(PostUpdateEventArgs $eventArguments): void
    {
        $this->reverseTransform($eventArguments, $eventArguments->getObject());
    }

    /**
     * @throws Exception
     */
    protected function transform(EventArgs $eventArguments): void
    {
        $eventAdapter = $this->getEventAdapter($eventArguments);
        $objectManager = $eventAdapter->getObjectManager();
        $unitOfWork = $objectManager->getUnitOfWork();

        foreach ($eventAdapter->getScheduledObjectUpdates($unitOfWork) as $entity) {
            $this->handle($eventAdapter, $objectManager, $unitOfWork, $entity, TransformerMethod::TRANSFORM);
        }

        foreach ($eventAdapter->getScheduledObjectInsertions($unitOfWork) as $entity) {
            $this->handle($eventAdapter, $objectManager, $unitOfWork, $entity, TransformerMethod::TRANSFORM);
        }
    }

    /**
     * @throws Exception
     */
    protected function reverseTransform(EventArgs $eventArguments, object $entity): void
    {
        $eventAdapter = $this->getEventAdapter($eventArguments);
        $objectManager = $eventAdapter->getObjectManager();
        $unitOfWork = $objectManager->getUnitOfWork();

        $this->handle($eventAdapter, $objectManager, $unitOfWork, $entity, TransformerMethod::REVERSE_TRANSFORM);
    }

    /**
     * @throws Exception
     */
    protected function handle(AdapterInterface $eventAdapter, EntityManagerInterface|ObjectManager $objectManager, UnitOfWork $unitOfWork, object $entity, TransformerMethod $method): void
    {
        $meta = $objectManager->getClassMetadata(get_class($entity));
        $config = $this->getConfiguration($objectManager, $meta->name);
        $transformableConfig = $config[self::TRANSFORMABLE] ?? [];
        if (!empty($transformableConfig)) {
            $changeSetBeforeTransformations = $unitOfWork->getEntityChangeSet($entity);
            foreach ($transformableConfig as $column) {
                $this->handleField($entity, $method, $column, $meta);
            }
            $eventAdapter->recomputeSingleObjectChangeSet($unitOfWork, $meta, $entity);
            $changeSetAfterTransformations = &$unitOfWork->getEntityChangeSet($entity);
            $changeSetAfterTransformations = array_intersect_key($changeSetAfterTransformations, $changeSetBeforeTransformations);
        }
    }

    /**
     * @throws Exception
     */
    protected function handleField(object $entity, TransformerMethod $method, array $column, ClassMetadata $meta): void
    {
        $field = $column['field'];
        $oid = spl_object_hash($entity);

        $reflectionProperty = $meta->getReflectionProperty($field);
        $oldValue = $this->getEntityValue($reflectionProperty, $entity);
        $newValue = $this->getNewValue($oid, $field, $column['name'], $method, $oldValue);
        $reflectionProperty->setValue($entity, $newValue);

        if ($method === TransformerMethod::REVERSE_TRANSFORM) {
            $this->storeOriginalFieldData($oid, $field, $oldValue, $newValue);
        }
    }

    protected function getEntityValue(ReflectionProperty $reflectionProperty, object $entity): string|null
    {
        $value = $reflectionProperty->getValue($entity);

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        return $value;
    }

    /**
     * @throws Exception
     */
    protected function getNewValue(string $oid, string $field, string $transformerName, TransformerMethod $method, mixed $value): mixed
    {
        if ($method === TransformerMethod::TRANSFORM
            && $this->getEntityFieldValue($oid, $field, TransformableState::PLAIN) === $value
        ) {
            return $this->getEntityFieldValue($oid, $field, TransformableState::TRANSFORMED);
        }

        return $this->performTransformerOperation($transformerName, $method, $value);
    }

    /**
     * @throws Exception
     */
    protected function performTransformerOperation(string $transformerName, TransformerMethod $method, mixed $oldValue): mixed
    {
        if (is_null($oldValue)) {
            return null;
        }
        $transformer = $this->getTransformer($transformerName);

        return match ($method) {
            TransformerMethod::TRANSFORM => $transformer->transform($oldValue),
            TransformerMethod::REVERSE_TRANSFORM => $transformer->reverseTransform($oldValue)
        };
    }

    protected function getEntityFieldValue(string $oid, string $field, TransformableState $state): mixed
    {
        if (!isset($this->entityFieldValues[$oid][$field])) {
            return null;
        }

        return $this->entityFieldValues[$oid][$field][$state->value];
    }

    protected function storeOriginalFieldData(string $oid, string $field, mixed $transformed, mixed $plain): void
    {
        $this->entityFieldValues[$oid][$field] = [
            TransformableState::TRANSFORMED->value => $transformed,
            TransformableState::PLAIN->value => $plain,
        ];
    }

    /**
     * @throws Exception
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

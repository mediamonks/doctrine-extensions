<?php

declare(strict_types=1);

/*
 * This file is part of the Doctrine Behavioral Extensions package.
 * (c) Gediminas Morkevicius <gediminas.morkevicius@gmail.com> http://www.gediminasm.org
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MediaMonks\Doctrine\Tests\Tool;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Base test case contains common mock objects
 * and functionality among all extensions using
 * ORM object manager
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 */
abstract class BaseTestCaseORM extends TestCase
{
    protected ?EntityManager $em;
    protected LoggerInterface $queryLogger;

    protected function setUp(): void
    {
        $this->queryLogger = $this->createMock(LoggerInterface::class);
    }

    /**
     * EntityManager mock object together with
     * annotation mapping driver and pdo_sqlite
     * database in memory
     */
    protected function getDefaultMockSqliteEntityManager(EventManager $evm = null, bool $annotations = false): EntityManager
    {
        try {
            $conn = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ]);
            $config = !$annotations ? $this->getDefaultConfiguration() : $this->getDefaultConfiguration($annotations);
            $em = new EntityManager($conn, $config, $evm ?: $this->getEventManager());
            $schema = array_map(static function ($class) use ($em) {
                return $em->getClassMetadata($class);
            }, $this->getUsedEntityFixtures());

            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema([]);
            $schemaTool->createSchema($schema);
            $this->em = $em;
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }

        return $this->em;
    }


    /**
     * Creates default mapping driver
     */
    protected function getMetadataDriverImplementation(bool $annotations): MappingDriver
    {
        if (!$annotations) {
            return new AttributeDriver([]);
        }

        return new AnnotationDriver(new AnnotationReader());
    }

    /**
     * Get a list of used fixture classes
     *
     * @phpstan-return list<class-string>
     */
    abstract protected function getUsedEntityFixtures(): array;

    protected function getDefaultConfiguration(bool $annotations = false): Configuration
    {
        $config = new Configuration();
        $config->setProxyDir(TESTS_TEMP_DIR);
        $config->setProxyNamespace('Proxy');
        $config->setMetadataDriverImpl($this->getMetadataDriverImplementation($annotations));

        // TODO: Remove the "if" check when dropping support of doctrine/dbal 2.
        if (class_exists(Middleware::class)) {
            $config->setMiddlewares([
                new Middleware($this->queryLogger),
            ]);
        }

        return $config;
    }

    /**
     * @return bool|array<string, mixed>
     */
    protected function fetchAssociative(string $query, array $params = [], array $types = []): array|bool
    {
        try {
            return $this->em->getConnection()->fetchAssociative($query, $params, $types);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function find($className, $id, $lockMode = null, $lockVersion = null): ?object
    {
        try {
            return $this->em->find($className, $id, $lockMode, $lockVersion);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function insert($table, array $data, array $types = []): int|string
    {
        try {
            return $this->em->getConnection()->insert($table, $data, $types);
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function persistAndFlush(object $entity): void
    {
        try {
            $this->em->persist($entity);
            $this->em->flush();
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function clear(): void
    {
        try {
            $this->em->clear();
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function flush(): void
    {
        try {
            $this->em->flush();
        } catch (Throwable $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function getEventManager(): EventManager
    {
        return new EventManager();
    }
}
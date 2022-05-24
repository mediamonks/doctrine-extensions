<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Cache\Adapter\ArrayAdapter as ArrayAdapterAlias;

define('TESTS_TEMP_DIR', sys_get_temp_dir().'/doctrine-extension-tests');

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Tool', __DIR__ . '/../vendor/gedmo/doctrine-extensions/tests/Gedmo/Tool');
$loader->add('Transformable\\Fixture', __DIR__ . '/src');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$reader                    = new AnnotationReader();
$reader                    = new PsrCachedReader($reader, new ArrayAdapterAlias());
$_ENV['annotation_reader'] = $reader;
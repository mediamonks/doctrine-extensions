<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Composer\Autoload\ClassLoader;

define('TESTS_PATH', __DIR__);
define('TESTS_TEMP_DIR', __DIR__ . '/temp');
define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->add('Gedmo\\Mapping\\Mock', __DIR__);
$loader->add('Tool', __DIR__ . '/../vendor/gedmo/doctrine-extensions/tests/Gedmo');

// fixture namespaces
$loader->add('Transformable\\Fixture', __DIR__ . '/src');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);
Gedmo\DoctrineExtensions::registerAnnotations();

$reader                    = new AnnotationReader();
$reader                    = new CachedReader($reader, new ArrayCache());
$_ENV['annotation_reader'] = $reader;
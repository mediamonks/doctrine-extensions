<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Composer\Autoload\ClassLoader;

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Tool', __DIR__ . '/../vendor/gedmo/doctrine-extensions/tests/Gedmo');
$loader->add('Transformable\\Fixture', __DIR__ . '/src');

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$reader                    = new AnnotationReader();
$reader                    = new CachedReader($reader, new ArrayCache());
$_ENV['annotation_reader'] = $reader;
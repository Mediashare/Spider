<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface as FrameworkBundleEngineInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Templating\EngineInterface as ComponentEngineInterface;

class AutowiringTypesTest extends AbstractWebTestCase
{
    public function testAnnotationReaderAutowiring()
    {
        static::bootKernel(['root_config' => 'no_annotations_cache.yml', 'environment' => 'no_annotations_cache']);

        $annotationReader = static::$container->get('test.autowiring_types.autowired_services')->getAnnotationReader();
        $this->assertInstanceOf(AnnotationReader::class, $annotationReader);
    }

    public function testCachedAnnotationReaderAutowiring()
    {
        static::bootKernel();

        $annotationReader = static::$container->get('test.autowiring_types.autowired_services')->getAnnotationReader();
        $this->assertInstanceOf(CachedReader::class, $annotationReader);
    }

    /**
     * @group legacy
     */
    public function testTemplatingAutowiring()
    {
        static::bootKernel(['root_config' => 'templating.yml', 'environment' => 'templating']);

        $autowiredServices = static::$container->get('test.autowiring_types.autowired_services');
        $this->assertInstanceOf(FrameworkBundleEngineInterface::class, $autowiredServices->getFrameworkBundleEngine());
        $this->assertInstanceOf(ComponentEngineInterface::class, $autowiredServices->getEngine());
    }

    public function testEventDispatcherAutowiring()
    {
        static::bootKernel(['debug' => false]);

        $autowiredServices = static::$container->get('test.autowiring_types.autowired_services');
        $this->assertInstanceOf(EventDispatcher::class, $autowiredServices->getDispatcher(), 'The event_dispatcher service should be injected if the debug is not enabled');

        static::bootKernel(['debug' => true]);

        $autowiredServices = static::$container->get('test.autowiring_types.autowired_services');
        $this->assertInstanceOf(TraceableEventDispatcher::class, $autowiredServices->getDispatcher(), 'The debug.event_dispatcher service should be injected if the debug is enabled');
    }

    public function testCacheAutowiring()
    {
        static::bootKernel();

        $autowiredServices = static::$container->get('test.autowiring_types.autowired_services');
        $this->assertInstanceOf(FilesystemAdapter::class, $autowiredServices->getCachePool());
    }

    protected static function createKernel(array $options = [])
    {
        return parent::createKernel(['test_case' => 'AutowiringTypes'] + $options);
    }
}

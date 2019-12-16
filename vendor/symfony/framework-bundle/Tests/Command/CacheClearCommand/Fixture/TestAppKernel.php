<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture;

use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestAppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    public function getProjectDir()
    {
        return __DIR__.'/test';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.\DIRECTORY_SEPARATOR.'config.yml');
    }

    public function setAnnotatedClassCache(array $annotatedClasses)
    {
        $annotatedClasses = array_diff($annotatedClasses, ['Symfony\Bundle\WebProfilerBundle\Controller\ExceptionController', 'Symfony\Bundle\TwigBundle\Controller\ExceptionController']);

        parent::setAnnotatedClassCache($annotatedClasses);
    }

    protected function build(ContainerBuilder $container)
    {
        $container->register('logger', NullLogger::class);
    }
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Console\Descriptor;

use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
abstract class Descriptor implements DescriptorInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function describe(OutputInterface $output, $object, array $options = [])
    {
        $this->output = $output;

        switch (true) {
            case $object instanceof RouteCollection:
                $this->describeRouteCollection($object, $options);
                break;
            case $object instanceof Route:
                $this->describeRoute($object, $options);
                break;
            case $object instanceof ParameterBag:
                $this->describeContainerParameters($object, $options);
                break;
            case $object instanceof ContainerBuilder && !empty($options['env-vars']):
                $this->describeContainerEnvVars($this->getContainerEnvVars($object), $options);
                break;
            case $object instanceof ContainerBuilder && isset($options['group_by']) && 'tags' === $options['group_by']:
                $this->describeContainerTags($object, $options);
                break;
            case $object instanceof ContainerBuilder && isset($options['id']):
                $this->describeContainerService($this->resolveServiceDefinition($object, $options['id']), $options, $object);
                break;
            case $object instanceof ContainerBuilder && isset($options['parameter']):
                $this->describeContainerParameter($object->resolveEnvPlaceholders($object->getParameter($options['parameter'])), $options);
                break;
            case $object instanceof ContainerBuilder:
                $this->describeContainerServices($object, $options);
                break;
            case $object instanceof Definition:
                $this->describeContainerDefinition($object, $options);
                break;
            case $object instanceof Alias:
                $this->describeContainerAlias($object, $options);
                break;
            case $object instanceof EventDispatcherInterface:
                $this->describeEventDispatcherListeners($object, $options);
                break;
            case \is_callable($object):
                $this->describeCallable($object, $options);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Object of type "%s" is not describable.', \get_class($object)));
        }
    }

    /**
     * Returns the output.
     *
     * @return OutputInterface The output
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool   $decorated
     */
    protected function write($content, $decorated = false)
    {
        $this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }

    /**
     * Describes an InputArgument instance.
     */
    abstract protected function describeRouteCollection(RouteCollection $routes, array $options = []);

    /**
     * Describes an InputOption instance.
     */
    abstract protected function describeRoute(Route $route, array $options = []);

    /**
     * Describes container parameters.
     */
    abstract protected function describeContainerParameters(ParameterBag $parameters, array $options = []);

    /**
     * Describes container tags.
     */
    abstract protected function describeContainerTags(ContainerBuilder $builder, array $options = []);

    /**
     * Describes a container service by its name.
     *
     * Common options are:
     * * name: name of described service
     *
     * @param Definition|Alias|object $service
     */
    abstract protected function describeContainerService($service, array $options = [], ContainerBuilder $builder = null);

    /**
     * Describes container services.
     *
     * Common options are:
     * * tag: filters described services by given tag
     */
    abstract protected function describeContainerServices(ContainerBuilder $builder, array $options = []);

    /**
     * Describes a service definition.
     */
    abstract protected function describeContainerDefinition(Definition $definition, array $options = []);

    /**
     * Describes a service alias.
     */
    abstract protected function describeContainerAlias(Alias $alias, array $options = [], ContainerBuilder $builder = null);

    /**
     * Describes a container parameter.
     */
    abstract protected function describeContainerParameter($parameter, array $options = []);

    /**
     * Describes container environment variables.
     */
    abstract protected function describeContainerEnvVars(array $envs, array $options = []);

    /**
     * Describes event dispatcher listeners.
     *
     * Common options are:
     * * name: name of listened event
     */
    abstract protected function describeEventDispatcherListeners(EventDispatcherInterface $eventDispatcher, array $options = []);

    /**
     * Describes a callable.
     *
     * @param mixed $callable
     */
    abstract protected function describeCallable($callable, array $options = []);

    /**
     * Formats a value as string.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function formatValue($value)
    {
        if (\is_object($value)) {
            return sprintf('object(%s)', \get_class($value));
        }

        if (\is_string($value)) {
            return $value;
        }

        return preg_replace("/\n\s*/s", '', var_export($value, true));
    }

    /**
     * Formats a parameter.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function formatParameter($value)
    {
        if (\is_bool($value) || \is_array($value) || (null === $value)) {
            $jsonString = json_encode($value);

            if (preg_match('/^(.{60})./us', $jsonString, $matches)) {
                return $matches[1].'...';
            }

            return $jsonString;
        }

        return (string) $value;
    }

    /**
     * @param string $serviceId
     *
     * @return mixed
     */
    protected function resolveServiceDefinition(ContainerBuilder $builder, $serviceId)
    {
        if ($builder->hasDefinition($serviceId)) {
            return $builder->getDefinition($serviceId);
        }

        // Some service IDs don't have a Definition, they're aliases
        if ($builder->hasAlias($serviceId)) {
            return $builder->getAlias($serviceId);
        }

        if ('service_container' === $serviceId) {
            return (new Definition(ContainerInterface::class))->setPublic(true)->setSynthetic(true);
        }

        // the service has been injected in some special way, just return the service
        return $builder->get($serviceId);
    }

    /**
     * @param bool $showHidden
     *
     * @return array
     */
    protected function findDefinitionsByTag(ContainerBuilder $builder, $showHidden)
    {
        $definitions = [];
        $tags = $builder->findTags();
        asort($tags);

        foreach ($tags as $tag) {
            foreach ($builder->findTaggedServiceIds($tag) as $serviceId => $attributes) {
                $definition = $this->resolveServiceDefinition($builder, $serviceId);

                if ($showHidden xor '.' === ($serviceId[0] ?? null)) {
                    continue;
                }

                if (!isset($definitions[$tag])) {
                    $definitions[$tag] = [];
                }

                $definitions[$tag][$serviceId] = $definition;
            }
        }

        return $definitions;
    }

    protected function sortParameters(ParameterBag $parameters)
    {
        $parameters = $parameters->all();
        ksort($parameters);

        return $parameters;
    }

    protected function sortServiceIds(array $serviceIds)
    {
        asort($serviceIds);

        return $serviceIds;
    }

    /**
     * Gets class description from a docblock.
     */
    public static function getClassDescription(string $class, string &$resolvedClass = null): string
    {
        $resolvedClass = $class;
        try {
            $resource = new ClassExistenceResource($class, false);

            // isFresh() will explode ONLY if a parent class/trait does not exist
            $resource->isFresh(0);

            $r = new \ReflectionClass($class);
            $resolvedClass = $r->name;

            if ($docComment = $r->getDocComment()) {
                $docComment = preg_split('#\n\s*\*\s*[\n@]#', substr($docComment, 3, -2), 2)[0];

                return trim(preg_replace('#\s*\n\s*\*\s*#', ' ', $docComment));
            }
        } catch (\ReflectionException $e) {
        }

        return '';
    }

    private function getContainerEnvVars(ContainerBuilder $container): array
    {
        if (!$container->hasParameter('debug.container.dump')) {
            return [];
        }

        if (!is_file($container->getParameter('debug.container.dump'))) {
            return [];
        }

        $file = file_get_contents($container->getParameter('debug.container.dump'));
        preg_match_all('{%env\(((?:\w++:)*+\w++)\)%}', $file, $envVars);
        $envVars = array_unique($envVars[1]);

        $bag = $container->getParameterBag();
        $getDefaultParameter = function (string $name) {
            return parent::get($name);
        };
        $getDefaultParameter = $getDefaultParameter->bindTo($bag, \get_class($bag));

        $getEnvReflection = new \ReflectionMethod($container, 'getEnv');
        $getEnvReflection->setAccessible(true);

        $envs = [];

        foreach ($envVars as $env) {
            $processor = 'string';
            if (false !== $i = strrpos($name = $env, ':')) {
                $name = substr($env, $i + 1);
                $processor = substr($env, 0, $i);
            }
            $defaultValue = ($hasDefault = $container->hasParameter("env($name)")) ? $getDefaultParameter("env($name)") : null;
            if (false === ($runtimeValue = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name))) {
                $runtimeValue = null;
            }
            $processedValue = ($hasRuntime = null !== $runtimeValue) || $hasDefault ? $getEnvReflection->invoke($container, $env) : null;
            $envs["$name$processor"] = [
                'name' => $name,
                'processor' => $processor,
                'default_available' => $hasDefault,
                'default_value' => $defaultValue,
                'runtime_available' => $hasRuntime,
                'runtime_value' => $runtimeValue,
                'processed_value' => $processedValue,
            ];
        }
        ksort($envs);

        return array_values($envs);
    }
}

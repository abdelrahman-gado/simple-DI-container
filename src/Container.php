<?php

declare(strict_types=1);

namespace AbdelrahmanGado\SimpleDIContainer;

use AbdelrahmanGado\SimpleDIContainer\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entires = [];

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entires[$id];

            if (is_callable($entry)) {
                return $entry($this);
            }

            $id = $entry;
        }

        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entires[$id]);
    }

    public function set(string $id, callable|string $concrete): void
    {
        $this->entires[$id] = $concrete;
    }

    public function resolve(string $id): object
    {
        $reflectionClass = new ReflectionClass($id);
        if (!$reflectionClass->isInstantiable()) {
            throw new ContainerException('Class "' . $id . '" is not Instantiable');
        }

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();
        if (!$parameters) {
            return new $id;
        }

        return $reflectionClass->newInstanceArgs($this->getDependencies($parameters, $id));
    }

    /**
     * Get an array of dependencies of the $id class
     * @param  /ReflectionParameter[]  $parameters
     * @param  string $id
     * @return array   
     */
    public function getDependencies(array $parameters, string $id): array
    {
        return array_map(
            function (ReflectionParameter $param) use ($id) {
                $name = $param->getName();
                $type = $param->getType();
                if (!$type) {
                    throw new ContainerException('Failed to resolve class "' . $id . '" because param "' . $name . '" is missing a type hint');
                }

                if ($type instanceof ReflectionUnionType) {
                    throw new ContainerException('Failed to resolve class "' . $id . '" because of union type for param "' . $name . '" is union type');
                }

                if (
                    $type instanceof ReflectionType &&
                    !$type->isBuiltin()
                ) {
                    return $this->get($type->getName());
                }

                throw new ContainerException('Failed to resolve class "' . $id . '" because invalid param "' . $name . '"');
            },
            $parameters
        );
    }
}

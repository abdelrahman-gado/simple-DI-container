<?php

declare(strict_types=1);

namespace Tests;

use AbdelrahmanGado\SimpleDIContainer\Container;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ContainerTest extends TestCase
{
    private readonly Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testHasMethodReturnFalseOnNotFoundClass()
    {
        $this->assertFalse($this->container->has('Class'));
    }

    public function testGettingNotFoundClassFromContainer()
    {
        // when try to get unset class from container.
        $this->expectException(ReflectionException::class);
        $this->container->get('DummyClass');
    }

    public function testSettingClassIntoContainer()
    {
        // when setting a specific class to the container
        $this->container->set('DummyClass', function () {
            return 'DummyClass';
        });

        $this->assertTrue($this->container->has('DummyClass'));
        $this->assertSame('DummyClass', $this->container->get('DummyClass'));
    }
}

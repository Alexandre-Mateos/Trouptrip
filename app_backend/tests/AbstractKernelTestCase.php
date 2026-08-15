<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractKernelTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function get(string $className): ?object
    {
        return static::getContainer()->get($className);
    }
}

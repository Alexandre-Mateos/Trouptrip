<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimpleTest extends kernelTestCase
{
    public function testCiIntegration(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'alexandre.mateos@mail.com']);

        $this->assertNotNull($user);
        $this->assertSame('Alexandre', $user->getFirstName());
    }
}

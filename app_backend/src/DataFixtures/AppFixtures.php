<?php

namespace App\DataFixtures;

use App\Story\BasicStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BasicStory::load();

        $manager->flush();
    }
}

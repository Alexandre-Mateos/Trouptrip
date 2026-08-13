<?php

namespace App\DataFixtures;

use App\Story\AssignmentTestStory;
use App\Story\BasicStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BasicStory::load();
        AssignmentTestStory::load();

        $manager->flush();
    }
}

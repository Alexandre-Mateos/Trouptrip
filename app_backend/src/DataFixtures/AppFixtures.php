<?php

namespace App\DataFixtures;

use App\Story\AssignmentTestStory;
use App\Story\BasicStory;
use App\Story\TripTestStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BasicStory::load();
        AssignmentTestStory::load();
        TripTestStory::load();

        $manager->flush();
    }
}

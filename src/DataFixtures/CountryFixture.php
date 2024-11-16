<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CountryFixture extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['seeder'];
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist(new Country("AT"));
        $manager->persist(new Country("DE"));
        $manager->persist(new Country("US"));
        $manager->flush();
    }
}

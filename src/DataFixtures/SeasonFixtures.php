<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    const SAISONS = [
        '1' => [
            'number' => 1,
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
            'year' => 2010
        ],
        '2' => [
            'number' => 2,
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
            'year' => 2011
        ],
        '3' => [
            'number' => 3,
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
            'year' => 2012
        ],
        '4' => [
            'number' => 4,
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
            'year' => 2016
        ],
        '5' => [
            'number' => 5,
            'description' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
            'year' => 2202
        ],
    ];
    public function load(ObjectManager $manager)
    {
        foreach (ProgramFixtures::PROGRAMS as $title => $description) {
            foreach (self::SAISONS as $number => $seasonDescription) {
                $season = new Season();
                $season->setNumber($seasonDescription['number']);
                $season->setYear($seasonDescription['year']);
                $season->setDescription($seasonDescription['description']);
                $season->setProgram($this->getReference('program_' . $title));
                $manager->persist($season);
                $this->addReference('season_' . $title . '_' . $number, $season);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}

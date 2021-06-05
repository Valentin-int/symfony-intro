<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    private const YEAR = 2010;

    private const DESCRIPTION = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

    public function load(ObjectManager $manager)
    {
        for ($g = 0; $g < 5; $g++) {
            for ($i = 0; $i < 5; $i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setYear(self::YEAR + $i);
                $season->setDescription(self::DESCRIPTION . strval($i));
                $season->setProgram($this->getReference('program_' . $g));
                $manager->persist($season);
            }
            $this->addReference('season_' . $g, $season);
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

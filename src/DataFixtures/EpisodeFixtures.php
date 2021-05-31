<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private const TITLE = 'Delicious';

    private const NUMBER = 0;

    private const SYNOPSIS = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $episode = new Episode();
            $episode->setTitle(self::TITLE . strval($i));
            $episode->setNumber(self::NUMBER + $i);
            $episode->setSynopsis(self::SYNOPSIS . strval($i));
            for ($j = 0; $j < count([SeasonFixtures::class]); $j++) {
                $episode->setSeason($this->getReference('season_' . $j));
            }
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

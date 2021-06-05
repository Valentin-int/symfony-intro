<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Service\Slugify;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private const TITLE = 'Delicious';

    private const NUMBER = 0;

    private const SYNOPSIS = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";

    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        for ($g = 0; $g < 5; $g++) {
            for ($i = 0; $i < 5; $i++) {
                $episode = new Episode();
                $episode->setTitle(self::TITLE . $i);
                $episode->setSlug($this->slugify->generate($episode->getTitle()));
                $episode->setNumber(self::NUMBER + $i);
                $episode->setSynopsis(self::SYNOPSIS . $i);
                $episode->setSeason($this->getReference('season_' . $g));
                $manager->persist($episode);
            }
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

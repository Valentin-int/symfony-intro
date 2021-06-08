<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Service\Slugify;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    const EPISODES = [
        [
            'title' => "Episode 1",
            'number' => 1,
            'synopsis' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
        ],
        [
            'title' => "Episode 2",
            'number' => 2,
            'synopsis' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
        ],
        [
            'title' => "Episode 3",
            'number' => 3,
            'synopsis' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
        ],
        [
            'title' => "Episode 4",
            'number' => 4,
            'synopsis' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
        ],
        [
            'title' => "Episode 5",
            'number' => 5,
            'synopsis' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis,',
        ],
    ];
    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        foreach (ProgramFixtures::PROGRAMS as $programTitle => $programDescription) {
            foreach (SeasonFixtures::SAISONS as $seasonTitle => $seasonDescription) {
                foreach (self::EPISODES as $number => $episodeDescription) {
                    $episode = new Episode();
                    $episode->setTitle($episodeDescription['title']);
                    $episode->setSlug($this->slugify->generate($seasonTitle . '-' . $programTitle .'-' . $episode->getTitle()));
                    $episode->setNumber($episodeDescription['number']);
                    $episode->setSynopsis($episodeDescription['synopsis']);
                    $episode->setSeason($this->getReference('season_'. $programTitle . '_' . $seasonTitle));
                    $manager->persist($episode);
                }
            }
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

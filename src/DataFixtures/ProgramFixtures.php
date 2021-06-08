<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use App\Service\Slugify\Slugify as SlugifySlugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        'Bones' => [
            'summary' => 'Un entropologue judiciaire travaille avec le FBI',
            'poster' => 'https://picsum.photos/200/300/?blur=2',
            'category' => 'Horreur'
        ],
        'Lupin' => [
            'summary' => 'L\'histoire de Arsene lupin remis au goût du jour.',
            'poster' => 'https://picsum.photos/200/300/?blur=2',
            'category' => 'Action'
        ],
        'Viking' => [
            'summary' => 'Ragnar Lothbrok et son frère Rollo participent à une bataille contre les peuples baltes. Après le massacre, Ragnar a des visions du Dieu Odin et ses Valkyries. De retour dans son village, Ragnar se rend, en compagnie de son fils Bjorn, à Kattegat pour assister au Thing et afin que ce dernier subisse son rite de passage vers l\'âge adulte.',
            'poster' => 'https://picsum.photos/200/300/?blur=2',
            'category' => 'Aventure'
        ],
        'Le seigneurs des anneaux' => [
            'summary' => 'Deux hobbit s\'en vont en vacance à la montagne',
            'poster' => 'https://picsum.photos/200/300/?blur=2',
            'category' => 'Fantastique'
        ],
        'The boys' => [
            'summary' => 'Variation irrévérencieuse sûr des super héros.',
            'poster' => 'https://picsum.photos/200/300/?blur=2',
            'category' => 'Action'
        ],

    ];
    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $title => $description) {
            $program = new Program();
            $program->setTitle($title);
            $program->setSummary($description['summary']);
            $program->setPoster($description['poster']);
            $program->setCategory($this->getReference('category_' . $description['category']));
            $program->setOwner($this->getReference('user_admin'));
            for ($i = 0; $i < count(ActorFixtures::ACTORS); $i++) {
                $program->addActor($this->getReference('actor_' . $i));
            }
            $program->setSlug($this->slugify->generate($program->getTitle()));
            $manager->persist($program);
            $this->addReference('program_' . $title, $program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ActorFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class
        ];
    }
}

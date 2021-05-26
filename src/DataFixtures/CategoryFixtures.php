<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORY = [
        'Action',
        'Aventure',
        'Animation',
        'Fantastique',
        'Horreur'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORY as $key => $categoryName) {
            $category = new Category();
            $category ->setName($categoryName);

            $manager->persist($category);
        }
        $manager->flush();
    }
}
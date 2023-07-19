<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $c = [
            1 => [
                'nom' => 'robes',
                'description' => 'description categorie',
                'slug' => 'robes'
            ],
            2 => [
                'nom' => 'chemises',
                'description' => 'description categorie',
                'slug' => 'chemises'
            ],
            3 => [
                'nom' => 't-shirts',
                'description' => 'description categorie',
                'slug' => 't-shirts'
            ],
            4 => [
                'nom' => 'vestes',
                'description' => 'description categorie',
                'slug' => 'vestes'
            ],
        ];
        foreach ($c as $k => $value) {
            $categorie = new Categorie();
            $categorie->setNom($value['nom']);
            $categorie->setSlug($value['slug']);
            $categorie->setDescription($value['description']);
            $manager->persist($categorie);
            $this->addReference('categorie-' . $k, $categorie);
        }
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 150; $i++) {
            $categorie = $this->getReference('categorie-' . $faker->numberBetween(1, 3));
            $produit = new Produit();
            $produit->setNom($faker->sentence);
            $produit->setSlug($faker->slug);
            $produit->setDescription($faker->text);
            $produit->setSousTitre($faker->sentence);
            $produit->setOnline(true);
            $produit->setPrix($faker->randomFloat(2));
            $produit->setCreatedAt(new DateTime('now'));
            $produit->setImage($faker->imageUrl(640, 480, 'animals', true));
            if ($categorie instanceof Categorie || $categorie === null) {
                $produit->setCategories($categorie);
            } else {
                // Gérer le cas où la référence ne correspond pas à une instance valide de Categorie
                throw new Exception("Erreur : Impossible de trouver une catégorie appropriée pour ce produit.");
            }
            $manager->persist($produit);
        }
        $manager->flush();
    }
}

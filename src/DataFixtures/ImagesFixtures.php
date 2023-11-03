<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($img = 1; $img <=100; $img++){
            $image = new Images();
            $image->setName($faker->image(null, 640, 480));  // retourne false pour le moment
            $product = $this->getReference('prod-'.rand(1, 10));
            $image->setProducts($product);

             $manager->persist($image);
           }
        $manager->flush();
    }

    // Méthode nécessaire pour indiquer quelle fixture doit être exécutée avant pour raison de dépendance
    // (il faut des produits pour pouvoir dire à l'image à quel produit elle se rapporte)

    public function getDependencies(): array
    {
        return [
            ProductsFixtures::class
        ];
    }
}

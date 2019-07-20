<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Variant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends BaseFixture
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, 200, function (Product $product, $count) {
            $product->setTitle($this->faker->realText(60))
                ->setDescription($this->faker->realText(600));

        });
        $manager->flush();
    }
}

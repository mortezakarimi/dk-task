<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Variant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends BaseFixture
{
    public const TOTAL_PRODUCT = 150;

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, self::TOTAL_PRODUCT, function (Product $product, $count) {
            $product->setTitle($this->faker->realText(60))
                ->setDescription($this->faker->realText(600));

        });
        $manager->flush();
    }
}

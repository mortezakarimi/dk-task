<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Variant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VariantFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Variant::class, 500, function (Variant $variant, $count) {
            $variant->setColor($this->faker->safeColorName)
                ->setPrice($this->faker->numberBetween(1000, 100000000))
                ->setProduct($this->getReference(Product::class . '_' . $this->faker->unique(true)->numberBetween(1, 199)));

        });
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            ProductFixtures::class
        ];
    }
}

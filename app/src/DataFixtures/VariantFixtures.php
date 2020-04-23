<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Variant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VariantFixtures extends BaseFixture implements DependentFixtureInterface
{
    public const TOTAL_VARIANT = 300;

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Variant::class, self::TOTAL_VARIANT, function (Variant $variant, $count) {
            $colors = ['ffffff' => 'سفید', 'ff0000' => 'قرمز', '00ff00' => 'سبز', '0000ff' => 'آبی', 'ffff00' => 'زرد', 'ff00ff' => 'صورتی', '00ffff' => 'فیروزه‌ای', '888888' => 'خاکستری', '000000' => 'مشکی'];

            $colorCode = $this->faker->randomKey($colors);
            $variant->setColor($colors[$colorCode])
                ->setPrice($this->faker->numberBetween(1000, 100000000))
                ->setColorCode($colorCode)
                ->setProduct($this->getReference(Product::class . '_' . $this->faker->unique(true)->numberBetween(0, ProductFixtures::TOTAL_PRODUCT - 1)));

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

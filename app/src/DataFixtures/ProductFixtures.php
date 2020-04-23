<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends BaseFixture implements DependentFixtureInterface
{
    public const TOTAL_PRODUCT = 150;

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, self::TOTAL_PRODUCT, function (Product $product, $count) {
            /** @var User $user */
            $user = $this->getRandomReference(User::class);
            $product->setTitle($this->faker->realText(60))
                ->setDescription($this->faker->realText(600))
                ->setAuthor($user);

        });
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}

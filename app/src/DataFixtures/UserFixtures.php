<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $adminUser = new User();
        $adminUser
            ->setNameFamily($this->faker->name)
            ->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword(
                $adminUser,
                '123456'
            ));
        $manager->persist($adminUser);

        $this->createMany(User::class, 100, function (User $user, $count) {
            $user->setEmail($this->faker->email)
                ->setNameFamily($this->faker->name)
                ->setRoles([$this->faker->optional(0.5, null)->randomElement(['ROLE_PRODUCT_ADMIN'])])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    '123456'
                ));
        });
        $manager->flush();
    }
}

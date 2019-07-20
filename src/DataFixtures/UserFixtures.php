<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
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
        $this->createMany(User::class, 100, function (User $user, $count) {
            $user->setEmail($this->faker->email)
                ->setNameFamily($this->faker->name)
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    '123456'
                ));
        });
        $manager->flush();
    }
}

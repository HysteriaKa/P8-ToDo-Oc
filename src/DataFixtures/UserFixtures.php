<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    
    protected $hashPwd;

    public function __construct(UserPasswordHasherInterface $hashPwd){

        $this->hashPwd = $hashPwd;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 12; $i++) {
            $user = (new User());
            $hash = $this->hashPwd->hashPassword($user, $faker->password());
            $user->setUsername($faker->userName())
                ->setPassword($hash)
                ->setEmail($user->getUsername() . '@email.com');
           
                if (mt_rand(1, 4) == 1) {
                $user->setRoles(["ROLE_ADMIN"]);
            }
            $manager->persist($user);
        }
        
        // $manager->persist($product);

        $manager->flush();
    }
}

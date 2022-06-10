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

    public function __construct(UserPasswordHasherInterface $hashPwd)
    {

        $this->hashPwd = $hashPwd;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $usersData  = [
            [
                'id' => 1,
                'password' => '123456',
                'username' => 'karine2310',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'id' => 2,
                'password' => $faker->password(),
                'username' => $faker->userName(),
                'roles' => ['ROLE_USER'],
            ],
            [
                'id' => 3,
                'password' => $faker->password(),
                'username' => "anonymous",
                'roles' => ['ROLE_USER'],
            ]
        ];
        foreach ($usersData as $userData) {
            $user = new User();
            $hash = $this->hashPwd->hashPassword($user, $userData['password']);
            $user->setUsername($userData['username'])
                ->setPassword($hash)
                ->setEmail($userData['username'] . '@gmail.com')
                ->setRoles($userData['roles']);
            $this->addReference('user-' . $userData['id'],$user);

            $manager->persist($user);
        }
       
        for ($i=0; $i < 12; $i++) { 
        
        $multiUsers = new User();
        $hash = $this->hashPwd->hashPassword($multiUsers, $faker->password());
        $multiUsers->setUsername($faker->userName())
        ->setPassword($hash)
        ->setEmail($multiUsers->getUsername(). '@email.com')
        ->setRoles(['ROLE_USER']);

        $manager->persist($multiUsers);
    }
        $manager->flush();
    }
}

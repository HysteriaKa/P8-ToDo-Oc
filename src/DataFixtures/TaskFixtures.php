<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {

        $this->doctrine = $doctrine;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->doctrine->getRepository(User::class)->findAll();
        $tasksData = [
            [
                'title' => $faker->sentence($nbWords = 10, $variableNbWords = true),
                'content' => $faker->sentence($nbWords = 42, $variableNbWords = true),
                'user-id' => 1,
                'done' => false
            ],
            [
                'title' => $faker->sentence($nbWords = 10, $variableNbWords = true),
                'content' => $faker->sentence($nbWords = 42, $variableNbWords = true),
                'user-id' => 2,
                'done' => true
            ],
            [
                'title' => $faker->sentence($nbWords = 10, $variableNbWords = true),
                'content' => $faker->sentence($nbWords = 42, $variableNbWords = true),
                'user-id' => 2,
                'done' => false
            ],
            [
                'title' => $faker->sentence($nbWords = 10, $variableNbWords = true),
                'content' => $faker->sentence($nbWords = 42, $variableNbWords = true),
                'user-id' => 3,
                'done' => false
            ],
            [
                'title' => $faker->sentence($nbWords = 10, $variableNbWords = true),
                'content' => $faker->sentence($nbWords = 42, $variableNbWords = true),
                'user-id' => 4,
                'done' => false
            ],

        ];
        foreach ($tasksData as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title'])
                ->setContent($taskData['content'])
                ->setUser($this->getReference('user-'.$taskData['user-id']))
                ->setDone($taskData['done']);

            $manager->persist($task);
        }
        
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use function Symfony\Component\ErrorHandler\Tests\testHeader;


abstract class BaseFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;
    /**
     * @property ObjectManager
     */
    protected $manager;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->manager = $manager;

        $this->loadData($manager);
    }

    abstract function loadData(ObjectManager $manager);

    protected function create(string $className, callable $factory)
    {
        $entity = new $className();
        $factory($entity);

        $this->manager->persist($entity);

        return $entity;
    }

    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $this->create($className, $factory);
            $this->addReference("$className|$i", $entity);
        }
        $this->manager->flush();
    }

    private $referencesIndex = [];

    protected function getRandomReference($className)
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $reference) {
                if (strpos($key, $className . '|') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
            if (empty($this->referencesIndex[$className])) {
                throw new \Exception('Не найденны ссылки на класс: ' . $className);
            }
            return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
        }
}

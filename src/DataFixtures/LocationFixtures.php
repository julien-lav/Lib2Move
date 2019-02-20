<?php

namespace App\DataFixtures;

 
use App\Entity\Location;
use App\Entity\User;
use App\Entity\Vehicule;
use App\Entity\Contrat;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\VehiculeFixtures;
use App\DataFixtures\ContratFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
 
class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $newLocation = new Location();

            $date = $faker->dateTimeThisDecade($max = 'now', $timezone = 'Europe/Paris');
            $start = new \DateTimeImmutable($date->format('Y-m-d H:i:s'));
            $newLocation->setStart($start);
            $end = $date->add(new \DateInterval('P'.rand(0, 3).'DT'.rand(1, 10).'H'.rand(0, 59).'M'));
            $newLocation->setEnd($end);

            // $newLocation->setKilometers(rand(10, 100));
            // $newLocation->setTime(new \DateTime('00:'.rand(10, 50)));
            $newLocation->setStatus('En cours');
            $newLocation->setCreatedAt(new \DateTime('now'));

            $newLocation->setUser($this->getReference(User::class.'_'.rand(0, 7)));
            $newLocation->setVehicule($this->getReference(Vehicule::class.'_'.rand(0, 19)));
            $newLocation->setContrat($this->getReference(Contrat::class.'_'.rand(0, 2).'_'.rand(0, 2)));
            
            $manager->persist($newLocation);

            $this->addReference(Location::class.'_'.$i, $newLocation);
        }

        $manager->flush(); 
    }

    public function getDependencies()
    {
        return [UserFixtures::class, VehiculeFixtures::class, ContratFixtures::class];
    }
}

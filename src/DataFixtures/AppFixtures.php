<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Posting;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('pl');

        $category1 = new Category();
        $category1->setName($faker->name);
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Praca');
        $manager->persist($category2);

        $posting = new Posting();
        $posting->setCategory($category1);

        $user = new User();
        $user->setEmail('nazwa@admin.pl')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$zaPTPRCNIsHmUglga1xgvA$iDxaeM3djAacfALh7WBwlaHupJ1fhjyaQQlEaODhpFI')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }
}

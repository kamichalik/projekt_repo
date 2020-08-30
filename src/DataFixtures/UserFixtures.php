<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class UserFixtures extends AbstractBaseFixtures
{
    public function loadData(ObjectManager $manager): void
    {

//        $category1 = new Category();
//        $category1->setName($faker->word);
//        $manager->persist($category1);
//
//        $category2 = new Category();
//        $category2->setName('Praca');
//        $manager->persist($category2);
//
//        $posting = new Posting();
//        $posting->setCategory($category1);

        $user = new User();
        $user->setEmail('nazwa@admin.pl')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$zaPTPRCNIsHmUglga1xgvA$iDxaeM3djAacfALh7WBwlaHupJ1fhjyaQQlEaODhpFI')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }


}

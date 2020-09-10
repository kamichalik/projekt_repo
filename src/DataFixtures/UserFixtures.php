<?php
/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager
     *
     */
    public function loadData(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('nazwa@admin.pl')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$zaPTPRCNIsHmUglga1xgvA$iDxaeM3djAacfALh7WBwlaHupJ1fhjyaQQlEaODhpFI')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }
}

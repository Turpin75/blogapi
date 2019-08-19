<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Users;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $pseudos = ['Andre', 'Pierre', 'Gignac'];
        $emails = ['andre@andre.fr', 'pierre@pierre.fr', 'gignac@gignac.fr'];
        $mdps = ['123', '231', '321'];

        for($i = 0; $i < 3; $i++){
            $user = new Users();
            $user->setPseudo($pseudos[$i])
                ->setEmail($emails[$i])
                ->setPassword($mdps[$i])
                ->setApiToken(strval(mt_rand()))
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

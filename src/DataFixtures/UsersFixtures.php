<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $sluger                       
    ){}

    public function load(ObjectManager $manager): void
    {

        $admin = new Users();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Vanremortère');
        $admin->setFirstname('Olivier');
        $admin->setAddress('12 rue de la Rue');
        $admin->setZipcode('75001');
        $admin->setCity('Paris');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);


        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <=5; $usr++){
           $user = new Users();
           $user->setEmail($faker->email);
           $user->setLastname($faker->lastName);
           $user->setFirstname($faker->firstName);
           $user->setAddress($faker->streetAddress);
           $user->setZipcode(str_replace(' ', '', $faker->postcode));
           $user->setCity($faker->city);
           $user->setPassword(
                $this->passwordEncoder->hashPassword($admin, 'secret')
            );
    
            $manager->persist($user);
    


        }

        $manager->flush();
    }
}

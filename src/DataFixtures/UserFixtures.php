<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user-';
    private $hasher;
    private $userDatas = [
        [
            'username' => 'toto',
            'password' => 'toto',
            'email' => 'mirtille@pommemail.com',
            'roles' => ['ROLE_USER']
        ],
        [
            'username' => 'alain',
            'password' => 'alain',
            'email' => 'cassis@pommemail.com',
            'roles' => ['ROLE_USER']
        ],
        [
            'username' => 'aline',
            'password' => 'aline',
            'email' => 'kiwi@pommemail.com',
            'roles' => ['ROLE_ADMIN']
        ],
    ];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->userDatas as $key => $userData)
        {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $password = $this->hasher->hashPassword($user, $userData['password']);
            $user->setPassword($password);
            $user->setRoles($userData['roles']);
            $this->setReference(self::USER_REFERENCE.$key, $user);
            $manager->persist($user);
            $manager->flush();
        }
    }
}

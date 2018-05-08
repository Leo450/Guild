<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder){
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
	{
		$userAdmin = new User();
		$userAdmin->setUsername("admin");
		$userAdmin->setPassword($this->encoder->encodePassword($userAdmin, "test"));
		$userAdmin->setRoles(["ROLE_ADMIN"]);

		$manager->persist($userAdmin);
		$manager->flush();
	}

}
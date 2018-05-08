<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 * @UniqueEntity("name")
 */
class Member
{

	public static $classes = [
		"Guerrier" => 1,
		"Paladin" => 2,
		"Chasseur" => 3,
		"Voleur" => 4,
		"Prêtre" => 5,
		"Chevalier de la mort" => 6,
		"Chaman" => 7,
		"Mage" => 8,
		"Démoniste" => 9,
		"Moine" => 10,
		"Druide" => 11,
		"Chasseur de démons" => 12,
	];
	public static $races = [
		"Humain" => 1,
		"Orc" => 2,
		"Nain" => 3,
		"Elfe de la nuit" => 4,
		"Mort vivant" => 5,
		"Tauren" => 6,
		"Gnome" => 7,
		"Troll" => 8,
		"Gobelin" => 9,
		"Elfe de sang" => 10,
		"Draenei" => 11,
		"Worgen" => 22,
		"Pandaren" => 25,
		"Elfe du vide" => 29,
	];
	public static $genders = [
		"Homme" => 1,
		"Femme" => 2,
	];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $class;

    /**
     * @ORM\Column(type="integer")
     */
    private $race;

    /**
     * @ORM\Column(type="integer")
     */
    private $gender;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $thumbnail;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $disponibilities;

	/**
	 * @ORM\OneToOne(targetEntity=User::class, inversedBy="member")
	 */
	private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getClass(): ?int
    {
        return $this->class;
    }

    public function setClass(int $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getRace(): ?int
    {
        return $this->race;
    }

    public function setRace(int $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

	public function getThumbnail(): ?string
	{
		return $this->thumbnail;
	}

	public function setThumbnail(string $thumbnail): self
	{
		$this->thumbnail = $thumbnail;

		return $this;
	}

    public function getDisponibilities(): ?array
    {
        return $this->disponibilities;
    }

    public function setDisponibilities(?array $disponibilities): self
    {
        $this->disponibilities = $disponibilities;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

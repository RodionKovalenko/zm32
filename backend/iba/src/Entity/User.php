<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: User::class)]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $firstname;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $lastname;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private $mitarbeiterId;


    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     */
    public function getMitarbeiterId()
    {
        return $this->mitarbeiterId;
    }

    /**
     * @param mixed $mitarbeiterId
     */
    public function setMitarbeiterId($mitarbeiterId): void
    {
        $this->mitarbeiterId = $mitarbeiterId;
    }
}
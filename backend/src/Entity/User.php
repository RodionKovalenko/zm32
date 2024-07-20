<?php

namespace App\Entity;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    use TimestampableEntity;
    use BlameableEntity;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     */
    public function getMitarbeiterId()
    {
        return $this->mitarbeiterId;
    }

    /**
     * @param mixed $mitarbeiterId
     * @return User
     */
    public function setMitarbeiterId($mitarbeiterId)
    {
        $this->mitarbeiterId = $mitarbeiterId;
        return $this;
    }
}


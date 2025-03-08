<?php

namespace App\Entity;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method string getUserIdentifier()
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $username;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $firstname;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $lastname;

    #[ORM\OneToOne(targetEntity: Mitarbeiter::class, mappedBy: 'user')]
    private ?Mitarbeiter $mitarbeiter = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private $mitarbeiterId;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $refreshTokenExpiry = null;

    #[ORM\Column(type: Types::STRING,  nullable: true)]
    private ?string $email = null;

    /** @var Collection<int, Rolle> */
    #[Groups(['User_Rolle', 'Rolle'])]
    #[ORM\ManyToMany(targetEntity: Rolle::class)]
    #[ORM\JoinTable(name: 'rolle_to_benutzer')]
    #[ORM\JoinColumn(name: 'benutzer_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'rolle_id', referencedColumnName: 'id', nullable: false)]
    private Collection $rolle;

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

    public function getMitarbeiter(): ?Mitarbeiter
    {
        return $this->mitarbeiter;
    }

    public function setMitarbeiter(?Mitarbeiter $mitarbeiter): User
    {
        $this->mitarbeiter = $mitarbeiter;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getRefreshTokenExpiry(): ?\DateTimeInterface
    {
        return $this->refreshTokenExpiry;
    }

    public function setRefreshTokenExpiry(?\DateTimeInterface $refreshTokenExpiry): User
    {
        $this->refreshTokenExpiry = $refreshTokenExpiry;
        return $this;
    }

    public function getRolle(): Collection
    {
        return $this->rolle;
    }

    public function setRolle(Collection $rolle): User
    {
        $this->rolle = $rolle;
        return $this;
    }


    public function getPassword()
    {
        return $this->getMitarbeiterId();
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    /**
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getRoles()
    {
        $roles = [];
        if (!empty($this->rolle)) {
            foreach ($this->rolle as $role) {
                /* @var Rolle $role */
                $roles[] = $role->getRole();
            }
        }

        if (empty($roles)) {
            $roles[] = Rolle::ROLE_USER;
        }

        return $roles;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
}


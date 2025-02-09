<?php

namespace App\Entity;

use App\Repository\RolleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolleRepository::class)]
class Rolle implements \Stringable
{
    /** Prefix, mit dem alle Rollen fuer Symfony beginnen muessen. */
    final public const ROLE_NAME_PREFIX = 'ROLE_';

    // Rollenkonstanten
    final public const ADMIN = 'ROLE_ADMIN';
    final public const ROLE_API = 'ROLE_API';
    final public const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: 'beschreibung', type: Types::STRING, length: 255, nullable: true)]
    private ?string $beschreibung = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        // Stelle sicher, dass der Rollenname mit ROLE_NAME_PREFIX beginnt
        if ($name !== null) {
            if (!str_starts_with($name, self::ROLE_NAME_PREFIX)) {
                $name = self::ROLE_NAME_PREFIX . $name;
            }
            $name = strtoupper(preg_replace('/[^A-Za-z0-9_]+/', '', str_replace(' ', '_', $name)));
        }
        $this->name = $name;

        return $this;
    }

    public function getBeschreibung(): ?string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(?string $beschreibung): self
    {
        $this->beschreibung = $beschreibung;

        return $this;
    }

    public function getRole()
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

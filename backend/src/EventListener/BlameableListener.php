<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Common\EventArgs;
use Symfony\Component\Security\Core\Security;
use Gedmo\Blameable\BlameableListener as GedmoBlameableListener;

class BlameableListener extends GedmoBlameableListener
{
    private $security;

    public function __construct(Security $security)
    {
        parent::__construct();
        $this->security = $security;
    }

    public function prePersist(EventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            $user = $this->security->getUser();
            if ($user) {
                $entity->setCreatedBy($user);
            }
            $entity->setCreatedAt(new \DateTime());
            $entity->setUpdatedAt(new \DateTime());
        }

        parent::prePersist($args);
    }

    public function preUpdate(EventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User) {
            $user = $this->security->getUser();
            if ($user) {
                $entity->setUpdatedBy($user);
            }
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}

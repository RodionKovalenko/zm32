<?php

namespace App\Security;

use Symfony\Component\Security\Core\Security;

class UserProvider
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getCurrentUser()
    {
        return $this->security->getUser();
    }
}
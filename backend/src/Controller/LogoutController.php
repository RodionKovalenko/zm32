<?php

namespace App\Controller;

use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route(path: '/api/logout')]
class LogoutController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly Security $security,
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/', name: 'app_login_logout', methods: ['POST'])]
    public function logout(Request $request)
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->getJsonResponse(['success' => false, 'message' => 'No active session found'], status: 401);
        }

        $user->setRefreshToken(null);
        $user->setRefreshTokenExpiry(null);
        $this->userRepository->save($user);

        return $this->getJsonResponse(['success' => true, 'message' => 'Logged out successfully']);
    }
}
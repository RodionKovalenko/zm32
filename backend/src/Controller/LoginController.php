<?php

namespace App\Controller;

use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route(path: '/api/login')]
class LoginController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
       // private readonly TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{mitarbeiterId}', name: 'app_login', methods: ['GET'])]
    public function loginMitarbeiter($mitarbeiterId, Request $request)
    {
        $data = ['success' => false, 'message' => 'Mitarbeiter Login ist fehlgeschlagen!'];

        $user = $this->userRepository->findOneBy(['mitarbeiterId' => $mitarbeiterId]);
        if ($user !== null) {
          // $currentUser = $this->tokenStorage->getToken()->getUser();
            $data = [
                'success' => true,
                'message' => 'Mitarbeiter Login successful!',
                'data' => [$user]
            ];
        }

        return $this->getJsonResponse($data, ['Default', 'Mitarbeiter']);
    }
}
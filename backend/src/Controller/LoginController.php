<?php

namespace App\Controller;

use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/login')]
class LoginController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly UserRepository $userRepository)
    {
        parent::__construct($serializer);
    }

    #[Route(path: '/homepage', name: 'app_api_homepage', methods: ['GET'])]
    public function login_check(Request $request)
    {
        $data = ['success' => true, 'message' => 'Login successful!'];
        return $this->getJsonResponse($data);
    }

    #[Route(path: '/{mitarbeiterId}', name: 'app_api_login', methods: ['GET'])]
    public function loginMitarbeiter($mitarbeiterId, Request $request)
    {
        $data = ['success' => false, 'message' => 'Mitarbeiter Login ist fehlgeschlagen!'];

        $user = $this->userRepository->find($mitarbeiterId);
        if ($user !== null) {
            $data = [
                'success' => true,
                'message' => 'Mitarbeiter Login successful!',
                'data' => [$user]
            ];
        }

        return $this->getJsonResponse($data, ['Default', 'Mitarbeiter']);
    }
}
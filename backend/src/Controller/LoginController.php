<?php

namespace App\Controller;

use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/login')]
class LoginController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{mitarbeiterId}', name: 'app_login', methods: ['GET'])]
    public function loginMitarbeiter($mitarbeiterId, Request $request)
    {
        $data = ['success' => false, 'message' => 'Mitarbeiter Login ist fehlgeschlagen!'];

        $user = $this->userRepository->findOneBy(['mitarbeiterId' => $mitarbeiterId]);
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
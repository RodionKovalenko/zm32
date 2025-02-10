<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/login')]
class LoginController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{mitarbeiterId}', name: 'app_login', methods: ['GET'])]
    public function loginMitarbeiter($mitarbeiterId, Request $request)
    {
        $data = ['success' => false, 'message' => 'Mitarbeiter Login ist fehlgeschlagen!'];

        /** @var User|null $user */
        $user = $this->userRepository->findOneBy(['mitarbeiterId' => $mitarbeiterId]);
        if ($user !== null) {
            // Generate a refresh token (random string)
            $refreshToken = bin2hex(random_bytes(32));
            // Save the refresh token in the database
            $user->setRefreshToken($refreshToken);
            $user->setRefreshTokenExpiry(new \DateTime('+10 hours'));
            $user->setUsername($user->getId() . '-' . $user->getMitarbeiterId() . '-' . $user->getFirstname());
            $this->userRepository->save($user);

            $jwtToken = $this->jwtManager->create($user);

            $data = [
                'success' => true,
                'message' => 'Mitarbeiter Login successful!',
                'data' => [
                    'user' => $user,
                    'jwt' => $jwtToken,
                    'refresh_token' => $refreshToken
                ]
            ];
        }

        return $this->getJsonResponse($data, ['Default', 'Mitarbeiter']);
    }

    #[Route(path: '/refresh-jwt-token', name: 'app_login_refresh_jwt_token', methods: ['POST'])]
    public function refreshJwtToken(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $refreshToken = $data['refresh_token'] ?? null;

        if (!$refreshToken) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Refresh token is missing']);
        }

        // Find user by refresh token
        $user = $this->userRepository->findOneBy(['refreshToken' => $refreshToken]);

        if (!$user || $user->getRefreshTokenExpiry() < new \DateTime()) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Invalid or expired refresh token'], status: 401);
        }

        // Generate new JWT
        $newJwtToken = $this->jwtManager->create($user);

        return $this->getJsonResponse(
            [
                'success' => true,
                'data' => ['jwt' => $newJwtToken]
            ]
        );
    }
}
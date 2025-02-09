<?php

namespace App\Security;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomJWTAuthenticator extends JWTAuthenticator
{
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepo;

    public function __construct(
        JWTManager $jwtManager,
        UserRepository $userRepo,
        EventDispatcherInterface $eventDispatcher,
        TokenExtractorInterface $tokenExtractor,
        UserProviderInterface $userProvider,
        private readonly LoggerInterface $logger,
        TranslatorInterface $translator = null
    ) {
        $this->jwtManager = $jwtManager;
        $this->userRepo = $userRepo;

        parent::__construct($jwtManager, $eventDispatcher, $tokenExtractor, $userProvider, $translator);
    }

    /**
     * This method decodes the JWT token and retrieves the user based on mitarbeiterId.
     */
    protected function getUserFromToken($token)
    {
        // Decode the token and retrieve the payload
        $payload = $this->jwtManager->decode($token);

        // Ensure the token contains 'mitarbeiterId' and throw an error if it's missing
        if (!isset($payload['username'])) {
            throw new CustomUserMessageAuthenticationException('Missing mitarbeiterId in token');
        }

        $this->logger->error("test rodion");
        $userId = implode('-', $payload['username'])[0];
        // Load user by mitarbeiterId
        return $this->userRepo->findOneBy(['id' => $userId]);
    }
}

<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class JWTAuthenticator implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        // Create a success response. This could include user details, or a custom success message
        $user = $token->getUser(); // Get the authenticated user

        // Example: Returning user info in the response, you can modify this based on your needs
        $response = [
            'message' => 'Authentication successful',
            'user' => [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(), // Add email if needed
            ],
        ];

        return new Response(json_encode($response), Response::HTTP_OK);
    }
}

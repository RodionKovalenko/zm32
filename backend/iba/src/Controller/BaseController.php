<?php

namespace App\Controller;

use App\Utils\JsonResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class BaseController
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function getJsonResponse($data, array $groups = ['Default']): JsonResponse
    {
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->enableMaxDepthChecks();

        return new JsonResponse($this->serializer, $data, 200, [], $context);
    }
}
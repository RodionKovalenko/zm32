<?php

namespace App\Utils;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response
{
    /**
     * JsonResponse constructor.
     *
     * @param null  $data
     * @param int   $status
     * @param array $headers
     * @param null  $context
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(SerializerInterface $jmsSerializer, $data = null, $status = 200, $headers = [], $context = null)
    {
        parent::__construct(($data !== null) ? $jmsSerializer->serialize($data, 'json', $context) : '', $status, $headers);
    }
}

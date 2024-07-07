<?php

namespace App\Controller;

use App\Repository\BestellungRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/bestellung')]
class BestellungController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly BestellungRepository $bestellungRepository)
    {
        parent::__construct($serializer);
    }

    #[Route(path: '/{departmentId}', name: 'app_bestellung_get_bestellung', methods: ['GET'])]
    public function getBestellung(int $departmentId, Request $request)
    {
        $bestellungen = $this->bestellungRepository->findBy(['department' => $departmentId]);

        $response = [
            'success' => true,
            'data' => $bestellungen
        ];

        return $this->getJsonResponse($response);
    }
}
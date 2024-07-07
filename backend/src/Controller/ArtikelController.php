<?php

namespace App\Controller;

use App\Repository\Material\ArtikelRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/artikel')]
class ArtikelController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly ArtikelRepository $artikelRepository)
    {
        parent::__construct($serializer);
    }

    #[Route(path: '/{departmentId}', name: 'app_artikel', methods: ['GET'])]
    public function getArtikels(int $departmentId, Request $request)
    {
        $artikels = $this->artikelRepository->findBy(['department' => $departmentId]);

        $response = [
            'success' => true,
            'data' => $artikels
        ];
        return $this->getJsonResponse($response);
    }
}
<?php

namespace App\Controller;

use App\Repository\Material\LieferantRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/lieferant')]
class LieferantController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly LieferantRepository $lieferantRepository, private readonly FormFactoryInterface $formFactory)
    {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{artikelId}', name: 'app_lieferant_get_lieferants', defaults: ['artikelId' => null], methods: ['GET'])]
    public function getLieferants($artikelId, Request $request)
    {
        if ($artikelId !== null) {
            $bestellungen = $this->lieferantRepository->getByArtikel($artikelId);
        } else {
            $bestellungen = $this->lieferantRepository->findAll();
        }

        $response = [
            'success' => true,
            'data' => $bestellungen
        ];

        return $this->getJsonResponse($response);
    }
}
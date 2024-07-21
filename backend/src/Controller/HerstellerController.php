<?php

namespace App\Controller;

use App\Entity\Material\Hersteller;
use App\Forms\HerstellerFormType;
use App\Repository\Material\HerstellerRepository;
use App\Repository\Material\HerstellerStandortRepository;
use Doctrine\Common\Collections\Order;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/hersteller')]
class HerstellerController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly HerstellerRepository $herstellerRepository,
        private readonly HerstellerStandortRepository $herstellerStandortRepository,
        private readonly FormFactoryInterface $formFactory
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{artikelId}', name: 'app_hersteller_get_herstellers', defaults: ['artikelId' => null], methods: ['GET'])]
    public function getHerstellers(int $artikelId, Request $request)
    {
        try {
            if (empty($artikelId)) {
                $hersteller = $this->herstellerRepository->findAllOrderedBy('name', Order::Ascending->value);
            } else {
                $hersteller = $this->herstellerRepository->getByArtikelId($artikelId);
            }

            $response = [
                'success' => true,
                'data' => $hersteller
            ];

            return $this->getJsonResponse(
                $response,
                [
                    'Default',
                    'Hersteller_HerstellerStandort'
                ]
            );
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    #[Route(path: '/save/{id}', name: 'app_hersteller_save_hersteller', defaults: ['id' => null], methods: ['POST'])]
    public function saveHersteller($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $herstellerId = $data['id'] ?? null;

        if ($id !== null && empty($herstellerId)) {
            $herstellerId = $id;
        }

        try {
            if (!empty($herstellerId)) {
                $hersteller = $this->herstellerRepository->find($herstellerId);
            } else {
                $hersteller = new Hersteller();
            }

            $form = $this->createForm(HerstellerFormType::class, $hersteller);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->herstellerRepository->save($hersteller);

                return $this->getJsonResponse(
                    [
                        'success' => true,
                        'data' => [$hersteller],
                        ['Default', 'Hersteller', 'Hersteller_HerstellerStandort']
                    ]
                );
            }

            return $this->getJsonResponse(['success' => false, 'message' => (string)$form->getErrors(true)]);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            return $this->getJsonResponse($response);
        }
    }

    #[Route(path: '/delete/{id}', name: 'app_hersteller_delete_hersteller', methods: ['POST'])]
    public function deleteHersteller($id, Request $request)
    {
        $hersteller = $this->herstellerRepository->find($id);

        if ($hersteller === null) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Hersteller wurde nicht gefunden']);
        }

        try {
            $this->herstellerRepository->delete($hersteller);
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->getJsonResponse(['success' => true]);
    }

    #[Route(path: '/deletestandort/{id}', name: 'app_hersteller_delete_herstellerstandort', methods: ['POST'])]
    public function deleteHerstellerStandort($id, Request $request)
    {
        $herstellerStandort = $this->herstellerStandortRepository->find($id);

        if ($herstellerStandort === null) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Herstellerstandort wurde nicht gefunden']);
        }

        try {
            $this->herstellerStandortRepository->delete($herstellerStandort);
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->getJsonResponse(['success' => true]);
    }
}
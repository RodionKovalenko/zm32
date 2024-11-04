<?php

namespace App\Controller;

use App\Entity\Material\Lieferant;
use App\Forms\LieferantFormType;
use App\Repository\Material\LieferantRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/lieferant')]
class LieferantController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly LieferantRepository $lieferantRepository, private readonly FormFactoryInterface $formFactory)
    {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/get_lieferant', name: 'app_lieferant_get_lieferant', methods: ['GET'])]
    public function getLieferants(Request $request)
    {
        try {
            $params = [];
            $search = $request->query->get('search') ?? null;

            if (!empty($search)) {
                $params['search'] = $search;
            }

            $lieferants = $this->lieferantRepository->getLieferantsByParams($params);

            $response = [
                'success' => true,
                'data' => $lieferants
            ];

            return $this->getJsonResponse(
                $response,
                [
                    'Default',
                    'Lieferant_LieferantStammdaten'
                ]
            );
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    #[Route(path: '/save/{id}', name: 'app_lieferant_save_lieferant', defaults: ['id' => null], methods: ['POST'])]
    public function saveLieferant($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $lieferantId = $data['id'] ?? null;

        if (empty($lieferantId)) {
            $lieferantId = $id;
        }

        try {
            if (!empty($lieferantId)) {
                $lieferant = $this->lieferantRepository->find($lieferantId);
            } else {
                $lieferant = new Lieferant();
            }

            $form = $this->createForm(LieferantFormType::class, $lieferant);

            $form->submit($data, false);

            if ($form->isValid()) {
                $this->lieferantRepository->save($lieferant);

                return $this->getJsonResponse(
                    [
                        'success' => true,
                        'data' => [$lieferant]
                    ],
                    ['Default', 'Lieferant_LieferantStammdaten']
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

    #[Route(path: '/delete/{id}', name: 'app_lieferant_delete_lieferant', defaults: ['id' => null], methods: ['POST'])]
    public function deleteLieferant($id, Request $request)
    {
        $lieferant = $this->lieferantRepository->find($id);
        $this->lieferantRepository->remove($lieferant);
    }
}
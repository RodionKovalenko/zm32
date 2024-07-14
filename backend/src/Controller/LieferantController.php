<?php

namespace App\Controller;

use App\Entity\Material\Lieferant;
use App\Forms\LieferantForm;
use App\Repository\Material\LieferantRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
            $lieferants = $this->lieferantRepository->getByArtikel($artikelId);
        } else {
            $lieferants = $this->lieferantRepository->findAll();
        }

        $response = [
            'success' => true,
            'data' => $lieferants
        ];

        return $this->getJsonResponse($response);
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
            if ($lieferantId !== null) {
                $lieferant = $this->lieferantRepository->find($lieferantId);
            } else {
                $lieferant = new Lieferant();
            }

            if (isset($data['lieferantStammdaten']) && !is_array($data['lieferantStammdaten'][0])) {
                $data['lieferantStammdaten'] = [$data['lieferantStammdaten']];
            }

            $form = $this->createForm(LieferantForm::class, $lieferant);

            $form->submit($data, false);

            if ($form->isValid()) {
                $this->lieferantRepository->save($lieferant);
                return $this->getJsonResponse(['success' => true, 'data' => [$lieferant]]);
            }

            return $this->getJsonResponse(['success' => false, 'message' => (string) $form->getErrors(true)]);
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
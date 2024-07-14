<?php

namespace App\Controller;

use App\Entity\Material\Artikel;
use App\Forms\ArtikelForm;
use App\Repository\DepartmentRepository;
use App\Repository\Material\ArtikelRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/artikel')]
class ArtikelController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly ArtikelRepository $artikelRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly DepartmentRepository $departmentRepository
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{departmentId}', name: 'app_artikel', methods: ['GET'])]
    public function getArtikels(int $departmentId, Request $request)
    {
        $departments = $this->departmentRepository->findBy(['id' => $departmentId]);
        $artikels = $this->artikelRepository->getByDepartmentId($departments);

        $response = [
            'success' => true,
            'data' => $artikels
        ];
        return $this->getJsonResponse($response);
    }

    #[Route(path: '/save/{id}', name: 'app_artikel_save_artikel', defaults: ['id' => null], methods: ['POST'])]
    public function saveArtikel($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $artikelId = $data['id'] ?? null;

        if (empty($artikelId)) {
            $artikelId = $id;
        }

        try {
            if ($artikelId !== null) {
                $artikel = $this->artikelRepository->find($artikelId);
            } else {
                $artikel = new Artikel();
            }

            $form = $this->createForm(ArtikelForm::class, $artikel);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->artikelRepository->save($artikel);
                return $this->getJsonResponse(['success' => true, 'data' => [$artikel]]);
            }

            return $this->getJsonResponse(['success' => false, 'message' => (string)$form->getErrors()]);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            return $this->getJsonResponse($response);
        }
    }

    #[Route(path: '/delete/{id}', name: 'app_artikel_delete_artikel', defaults: ['id' => null], methods: ['POST'])]
    public function deleteArtikel($id, Request $request)
    {
        $lieferant = $this->artikelRepository->find($id);
        $this->artikelRepository->remove($lieferant);
    }
}
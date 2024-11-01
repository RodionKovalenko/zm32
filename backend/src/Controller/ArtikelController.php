<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\Material\Artikel;
use App\Forms\ArtikelFormType;
use App\Repository\DepartmentRepository;
use App\Repository\Material\ArtikelRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/artikel')]
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

    #[Route(path: '/{departmentId}', name: 'app_artikel_get_artikels', defaults: ['departmentId' => null], methods: ['GET'])]
    public function getArtikels(int $departmentId, Request $request)
    {
        $params = [];
        $search = $request->query->get('search') ?? null;
        $departmentIds = $request->query->get('departments') ?? null;
        $withAssociatedDate = $request->query->get('withAssociatedData') ?? false;

        if (!empty($departmentIds)) {
            $departmentIds = explode(',', $departmentIds);
            $params['departmentIds'] = $departmentIds;

            $departments = $this->departmentRepository->findBy(['id' => $departmentIds]);

            // Check if department of type Alle is
            $isAllDepartment = array_filter($departments, function (Department $department) {
                return $department->getTyp() === DepartmentTyp::ALLE->value;
            });

            if (!empty($isAllDepartment)) {
                $params['departmentIds'] = null;
            }
        }

        if (!empty($search)) {
            $params['search'] = $search;
        }

        $artikels = $this->artikelRepository->getByParams($params);

        $response = [
            'success' => true,
            'data' => $artikels
        ];

        if ($withAssociatedDate) {
            return $this->getJsonResponse(
                $response,
                [
                    'Default',
                    'Artikel_Department',
                    'Artikel_Hersteller',
                    'Artikel_Lieferant',
                    'Artikel_ArtikelToLieferBestellnummer',
                    'Artikel_ArtikelToHerstRefnummer'
                ]
            );
        }

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

            $form = $this->createForm(ArtikelFormType::class, $artikel);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->artikelRepository->save($artikel);
                return $this->getJsonResponse(['success' => true, 'data' => [$artikel]],
                                              [
                                                  'Default',
                                                  'Artikel_Department',
                                                  'Artikel_Hersteller',
                                                  'Artikel_Lieferant',
                                                  'Artikel_ArtikelToLieferBestellnummer',
                                                  'Artikel_ArtikelToHerstRefnummer'
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

    #[Route(path: '/delete/{id}', name: 'app_artikel_delete_artikel', defaults: ['id' => null], methods: ['POST'])]
    public function deleteArtikel($id, Request $request)
    {
        try {
            /** @var Artikel $artikel */
            $artikel = $this->artikelRepository->find($id);

            $artikel->getDepartments()->clear();
            $artikel->getHerstellers()->clear();
            $artikel->getLieferants()->clear();
            $artikel->getArtikelToHerstRefnummers()->clear();
            $artikel->getArtikelToLieferantBestellnummers()->clear();

            $this->artikelRepository->remove($artikel);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            return $this->getJsonResponse($response);
        }

        $response = [
            'success' => true,
        ];

        return $this->getJsonResponse($response);
    }

    #[Route(path: '/get_by_id/{id}', name: 'app_artikel_get_artikel_by_id', methods: ['GET'])]
    public function getArtikelById(int $id, Request $request)
    {
        $artikel = $this->artikelRepository->find($id);

        $response = [
            'success' => true,
            'data' => [$artikel]
        ];
        return $this->getJsonResponse(
            $response,
            [
                'Default',
                'Artikel_Department',
                'Artikel_Hersteller',
                'Artikel_Lieferant',
                'Artikel_ArtikelToLieferBestellnummer',
                'Artikel_ArtikelToHerstRefnummer'
            ]
        );
    }

}
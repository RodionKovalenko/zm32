<?php

namespace App\Controller;

use App\Business\Export\BestellungExportHelper;
use App\Entity\Bestellung;
use App\Entity\BestellungStatus;
use App\Entity\DepartmentTyp;
use App\Entity\Mitarbeiter;
use App\Forms\BestellungFormType;
use App\Repository\BestellungRepository;
use App\Repository\DepartmentRepository;
use App\Repository\MitarbeiterRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/bestellung')]
class BestellungController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly BestellungRepository $bestellungRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly DepartmentRepository $departmentRepository,
        private readonly MitarbeiterRepository $mitarbeiterRepository,
        private readonly BestellungExportHelper $bestellungExportHelper
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/get_bestellungen', name: 'app_bestellung_get_bestellung', methods: ['GET'])]
    public function getBestellung(Request $request)
    {
        $departmentIds = $request->get('departments') ? json_decode($request->get('departments')) : [DepartmentTyp::ALLE];
        $status = $request->get('status') ? json_decode($request->get('status')) : [];
        $createdAfter = $request->get('createdAfter') ?? [];
        $datumBis = $request->get('datumBis') ?? [];
        $search = $request->get('search') ?? [];

        $filterParams = [];

        if (!empty($status)) {
            $filterParams['status'] = $status;
        }
        if (!empty($departmentIds)) {
            $departments = $this->departmentRepository->findBy(['id' => $departmentIds]);
            $filterParams['departments'] = $departments;
        }
        if (!empty($createdAfter)) {
            $filterParams['createdAfter'] = $createdAfter;
        }
        if (!empty($datumBis)) {
            $filterParams['datumBis'] = $datumBis;
        }
        if (!empty($search)) {
            $filterParams['search'] = $search;
        }

        $bestellungen = $this->bestellungRepository->getByDepartment($filterParams);

        $response = [
            'success' => true,
            'data' => $bestellungen
        ];

        return $this->getJsonResponse(
            $response,
            [
                'Default',
                'Bestellung_Mitarbeiter',
                'Bestellung_Artikel',
                'Bestellung_Department',
                'Bestellung_Lieferant',
                'Bestellung_Hersteller'
            ]
        );
    }

    #[Route(path: '/save/{id}', name: 'app_bestellung_save_bestellung', defaults: ['id' => null], methods: ['POST'])]
    public function saveBestellung($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $bestellungId = $data['id'] ?? null;

        if (empty($bestellungId)) {
            $bestellungId = $id;
        }

        try {
            if ($bestellungId && $bestellungId !== 0) {
                $bestellung = $this->bestellungRepository->find($bestellungId);
            } else {
                $bestellung = new Bestellung();
            }

            if ($bestellung->getDatum() === null) {
                $bestellung->setDatum(new \DateTime());
                $bestellung->setStatus(BestellungStatus::OFFEN->value);

                /** @var Mitarbeiter $mitarbeiter */
                $mitarbeiter = $this->mitarbeiterRepository->getMitarbeiterByUserMitarbeiterId($data['mitarbeiterId']);
                $bestellung->setMitarbeiter($mitarbeiter);
            }

            $form = $this->createForm(BestellungFormType::class, $bestellung);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->bestellungRepository->save($bestellung);
                return $this->getJsonResponse(['success' => true, 'data' => $bestellung], [
                    'Default',
                    'Bestellung_Mitarbeiter',
                    'Bestellung_Artikel',
                    'Bestellung_Department',
                    'Bestellung_Lieferant',
                    'Bestellung_Hersteller'
                ]);
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

    #[Route(path: '/update_status/{id}', name: 'app_bestellung_update_status', methods: ['POST'])]
    public function updateStatus($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            /** @var Bestellung $bestellung */
            $bestellung = $this->bestellungRepository->find($id);
            $status = filter_var($data['status'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            $bestellung->setStatus($status);

            $this->bestellungRepository->save($bestellung);
            return $this->getJsonResponse(['success' => true, 'data' => $bestellung], [
                'Default',
                'Bestellung_Mitarbeiter',
                'Bestellung_Artikel',
                'Bestellung_Department',
                'Bestellung_Lieferant',
                'Bestellung_Hersteller'
            ]);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            return $this->getJsonResponse($response);
        }
    }

    #[Route(path: '/delete/{id}', name: 'app_bestellung_delete_bestellung', methods: ['POST'])]
    public function deleteBestellung($id, Request $request)
    {
        $bestellung = $this->bestellungRepository->find($id);

        if ($bestellung === null) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Bestellung wurde nicht gefunden']);
        }

        try {
            $this->bestellungRepository->remove($bestellung);
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->getJsonResponse(['success' => true]);
    }

    #[Route(path: '/download_bestellungen', name: 'app_bestellung_download_bestellungen', methods: ['GET'])]
    public function downloadBestellungen(Request $request): void
    {
        $departmentIds = $request->get('departments') ? json_decode($request->get('departments')) : [DepartmentTyp::ALLE];
        $status = $request->get('status') ? json_decode($request->get('status')) : [];
        $createdAfter = $request->get('createdAfter') ?? [];

        $filterParams = [];

        if (!empty($status)) {
            $filterParams['status'] = $status;
        }
        if (!empty($departmentIds)) {
            $departments = $this->departmentRepository->findBy(['id' => $departmentIds]);
            $filterParams['departments'] = $departments;
        }
        if (!empty($createdAfter)) {
            $filterParams['createdAfter'] = $createdAfter;
        }

        $bestellungen = $this->bestellungRepository->getByDepartment($filterParams);

        $this->bestellungExportHelper->generateExport($bestellungen);
    }
}
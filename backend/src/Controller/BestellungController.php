<?php

namespace App\Controller;

use App\Entity\Bestellung;
use App\Entity\BestellungStatus;
use App\Entity\Mitarbeiter;
use App\Forms\BestellungFormType;
use App\Repository\BestellungRepository;
use App\Repository\DepartmentRepository;
use App\Repository\Material\LieferantRepository;
use App\Repository\MitarbeiterRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/bestellung')]
class BestellungController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly BestellungRepository $bestellungRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly DepartmentRepository $departmentRepository,
        private readonly MitarbeiterRepository $mitarbeiterRepository,
        private readonly LieferantRepository $lieferantRepository
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{departmentId}', name: 'app_bestellung_get_bestellung', methods: ['GET'])]
    public function getBestellung(int $departmentId, Request $request)
    {
        $departments = $this->departmentRepository->findBy(['id' => $departmentId]);
        $bestellungen = $this->bestellungRepository->getByDepartment($departments);

        $response = [
            'success' => true,
            'data' => $bestellungen
        ];

        return $this->getJsonResponse(
            $response,
            [
                'Default',
                'Bestellung_Artikel',
                'Bestellung_Mitarbeiter',
                'Artikel_Department',
                'Bestellung_Lieferant'
            ]
        );
    }

    #[Route(path: '/save/{id}', name: 'app_bestellung_save_bestellung', defaults: ['id' => null], methods: ['POST'])]
    public function saveBestellung($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $bestellungId = $data['id'] ?? null;

        if (empty($id)) {
            $id = $bestellungId;
        }

        try {
            if ($id !== null) {
                $bestellung = $this->bestellungRepository->find($id);
            } else {
                $bestellung = new Bestellung();
            }

            if ($bestellung->getDatum() === null) {
                $bestellung->setDatum(new \DateTime());
                $bestellung->setStatus(BestellungStatus::OFFEN->value);
            }

            $department = $this->departmentRepository->findOneBy(['id' => $data['department']]);
            $bestellung->addDepartment($department);

            $lieferant = $this->lieferantRepository->findOneBy(['id' => $data['lieferantId']]);
            $bestellung->setLieferant($lieferant);

            /** @var Mitarbeiter $mitarbeiter */
            $mitarbeiter = $this->mitarbeiterRepository->getMitarbeiterByUserMitarbeiterId($data['mitarbeiterId']);
            $bestellung->setMitarbeiter($mitarbeiter);

            $form = $this->createForm(BestellungFormType::class, $bestellung);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->bestellungRepository->save($bestellung);
                return $this->getJsonResponse(['success' => true, 'data' => [$bestellung]]);
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

    #[Route(path: '/delete/{id}', name: 'app_bestellung_delete_bestellung', methods: ['POST'])]
    public function deleteBestellung($id, Request $request)
    {
        $bestellung = $this->bestellungRepository->find($id);

        if ($bestellung === null) {
            return $this->getJsonResponse(['success' => false, 'message' => 'Bestellung wurde nicht gefunden']);
        }

        try {
            $this->bestellungRepository->delete($bestellung);
        } catch (\Exception $e) {
            return $this->getJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->getJsonResponse(['success' => true]);
    }
}
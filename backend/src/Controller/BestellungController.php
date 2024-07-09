<?php

namespace App\Controller;

use App\Entity\Bestellung;
use App\Entity\BestellungStatus;
use App\Entity\Mitarbeiter;
use App\Forms\BestellungForm;
use App\Repository\BestellungRepository;
use App\Repository\DepartmentRepository;
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
        private readonly MitarbeiterRepository $mitarbeiterRepository
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/{departmentId}', name: 'app_bestellung_get_bestellung', methods: ['GET'])]
    public function getBestellung(int $departmentId, Request $request)
    {
        $bestellungen = $this->bestellungRepository->getByDepartment($departmentId);

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
            ]
        );
    }

    #[Route(path: '/save/{id}', name: 'app_bestellung_save_bestellung', defaults: ['id' => null], methods: ['POST'])]
    public function getSaveBestellung($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);

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

            /** @var Mitarbeiter $mitarbeiter */
            $mitarbeiter = $this->mitarbeiterRepository->getMitarbeiterByUserMitarbeiterId($data['mitarbeiterId']);
            $bestellung->setMitarbeiter($mitarbeiter);

            $form = $this->createForm(BestellungForm::class, $bestellung);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->bestellungRepository->save($bestellung);
                return $this->getJsonResponse(['success' => true, 'data' => [$bestellung]]);
            }

            return $this->getJsonResponse(['data' => (string)$form->getErrors()]);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];

            return $this->getJsonResponse($response);
        }
    }

}
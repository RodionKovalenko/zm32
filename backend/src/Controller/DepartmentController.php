<?php

namespace App\Controller;

use App\Entity\Department;
use App\Forms\DepartmentFormType;
use App\Repository\DepartmentRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/department')]
class DepartmentController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly DepartmentRepository $departmentRepository, private readonly FormFactoryInterface $formFactory)
    {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/get_departments', name: 'app_department_get_departments', methods: ['GET'])]
    public function getDepartments(Request $request)
    {
        $params = [];
        $search = $request->query->get('search') ?? null;

        if (!empty($search)) {
            $params['search'] = $search;
        }

        $deparments = $this->departmentRepository->getDeparmentByParams($params);

        $response = [
            'success' => true,
            'data' => $deparments
        ];
        return $this->getJsonResponse($response);
    }

    #[Route(path: '/save/{id}', name: 'app_department_save', defaults: ['id' => null], methods: ['POST'])]
    public function saveDepartment($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $departmentId = $data['id'] ?? null;

        if (empty($departmentId)) {
            $departmentId = $id;
        }

        try {
            if ($departmentId !== null) {
                $department = $this->departmentRepository->find($departmentId);
            } else {
                $department = new Department();
            }

            $form = $this->createForm(DepartmentFormType::class, $department);
            $form->submit($data, false);

            if ($form->isValid()) {
                if ($department->getTyp() === null) {
                    $department->setTyp(7);
                }
                $this->departmentRepository->save($department);

                return $this->getJsonResponse(['success' => true, 'data' => [$department]]);
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

    #[Route(path: '/delete/{id}', name: 'app_department_delete_department', defaults: ['id' => null], methods: ['POST'])]
    public function deleteDepartment($id, Request $request)
    {
        try {
            /** @var Department $department */
            $department = $this->departmentRepository->find($id);
            $this->departmentRepository->remove($department);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Abteilung befindet sich im Einsatz und kann nicht gelöscht werden!'
            ];

            return $this->getJsonResponse($response);
        }

        $response = [
            'success' => true,
        ];

        return $this->getJsonResponse($response);
    }
}
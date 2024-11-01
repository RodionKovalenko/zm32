<?php

namespace App\Controller;

use App\Business\User\UserHandler;
use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/user')]
class UserController extends BaseController
{
    public function __construct(
        SerializerInterface $serializer,
        private readonly UserRepository $userRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly UserHandler $userHandler,
        private readonly FormFactoryInterface $formFactory
    ) {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/get_user', name: 'app_user_get_user', methods: ['GET'])]
    public function getUser(Request $request)
    {
        $params = [];
        $search = $request->query->get('search') ?? null;
        $departmentIds = $request->query->get('departments') ?? null;

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

        $user = $this->userRepository->getUserByParams($params);

        $response = [
            'success' => true,
            'data' => $user
        ];

        return $this->getJsonResponse($response);
    }


    #[Route(path: '/save/{id}', name: 'app_user_save', defaults: ['id' => null], methods: ['POST'])]
    public function saveUser($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $this->userHandler->saveUser($data);

            $response = [
                'success' => true,
                'message' => 'User saved successfully!',
                'data' => [$user]
            ];

            return $this->getJsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'User save failed!',
                'data' => $e->getMessage()
            ];
        }

        return $this->getJsonResponse($response);
    }

    #[Route(path: '/delete/{id}', name: 'app_user_delete', defaults: ['id' => null], methods: ['POST'])]
    public function deleteUser($id, Request $request)
    {
        try {
            $this->userHandler->deleteUser($id);

            $response = [
                'success' => true,
                'message' => 'User deleted successfully!'
            ];

            return $this->getJsonResponse($response);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'User delete failed!',
                'data' => $e->getMessage()
            ];
        }

        return $this->getJsonResponse($response);
    }

    #[Route(path: '/get_user_departments/{userId}', name: 'app_user_get_user_departments', methods: ['GET'])]
    public function getUserDepartments($userId)
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);
        $mitarbeiter = $user->getMitarbeiter();
        $departments = [];

        if ($mitarbeiter !== null) {
            $mitarbeiterDepartments = $mitarbeiter->getMitarbeiterToDepartments();

            $departments = array_map(function ($mitarbeiterDepartment) {
                return $mitarbeiterDepartment->getDepartment();
            }, $mitarbeiterDepartments->getValues());
        }

        $response = [
            'success' => true,
            'data' => $departments
        ];

        return $this->getJsonResponse($response);
    }
}
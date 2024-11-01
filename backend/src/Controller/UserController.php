<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\User;
use App\Forms\UserFormType;
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
        $userId = $data['id'] ?? null;

        if (empty($userId)) {
            $userId = $id;
        }

        try {
            if ($userId !== null) {
                $user = $this->userRepository->find($userId);
            } else {
                $user = new User();
            }

            $form = $this->createForm(UserFormType::class, $user);
            $form->submit($data, false);

            if ($form->isValid()) {
                $this->userRepository->save($user);

                $response = [
                    'success' => true,
                    'message' => 'User saved successfully!',
                    'data' => $user
                ];

                return $this->getJsonResponse($response);
            }

            return $this->getJsonResponse(['success' => false, 'message' => (string)$form->getErrors(true)]);
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
            $user = $this->userRepository->find($id);
            $this->userRepository->delete($user);

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
}
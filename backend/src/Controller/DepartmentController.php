<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/department')]
class DepartmentController extends BaseController
{
    public function __construct(SerializerInterface $serializer, private readonly DepartmentRepository $departmentRepository, private readonly FormFactoryInterface $formFactory)
    {
        parent::__construct($serializer, $this->formFactory);
    }

    #[Route(path: '/get_departments', name: 'app_department_get_departments', methods: ['GET'])]
    public function getDepartments(Request $request)
    {
        $deparments = $this->departmentRepository->findAll();

        $response = [
            'success' => true,
            'message' => 'Login successful!',
            'data' => $deparments
        ];
        return $this->getJsonResponse($response);
    }
}
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/index')]
class IndexController extends BaseController
{
    #[Route(path: '/materialliste', name: 'app_index_materialliste', methods: ['GET'])]
    public function getmaterialliste(Request $request)
    {
        $data = ['message' => 'Materialliste erfolgreich geladen!'];
        return $this->getJsonResponse($data);
    }
}
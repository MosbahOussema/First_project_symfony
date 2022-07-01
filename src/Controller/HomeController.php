<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/home_json1", format="json", name="home_json1", methods={"GET"})
     */
    public function home_json1(): JsonResponse
    {
        return new JsonResponse(['erreur' => false]);
    }

    /**
     * @Route("/home_json", format="json", name="home_json", methods={"POST"})
     */
    public function home_json(Request $request): JsonResponse
    {
        $data= [
            'firstName' => $request->request->get('firstname'),
            'lastName' => $request->request->get('lastname'),
            'age' => date_diff(new \Datetime(), new \Datetime($request->request->get('birthdate')))->y
        ];
        return new JsonResponse($data);
    }


}

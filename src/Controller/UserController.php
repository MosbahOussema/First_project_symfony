<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Service\Product as ProductService;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $productservice;
    private $userRepository;
    private $productRepository;

    /**
     * Product constructor
     */
    public function __construct(ProductService $productservice, UserRepository $userRepository, ProductRepository $productRepository ) // on declare notre service ici dan le constructeur pour evité l'appel dans chaque methode comme parametre 
    {
        $this->productservice=$productservice;
        $this->productRepository=$productRepository;
        $this->userRepository=$userRepository;
    }
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

     /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user); // liez l'objet form a la requette reçu
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }
     /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }
    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }
      /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

     


}
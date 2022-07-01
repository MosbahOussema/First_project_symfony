<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\Product as ProductService;// car on a entity de meme nom

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
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
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->searchByName($request->query->get('search')), // on va recuperé la variable de nom 'search' de les variable de request dand querry(et pas dans attribut (lien) comme category)
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product); // liez l'objet form a la requette reçu
        $form->handleRequest($request);// faire matching entre data dans request et objet $product

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product); //eq a insert
            $entityManager->flush(); // eq a commit

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/category/{category}", name="product_category", methods={"GET"})
     */
    public function category(Request $request, ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->searchByCategory($request->attributes->get('category')),
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

    /**
     * @Route("/user/{user}", name="user_addition", methods={"GET"})
     */
    public function user(Request $request,User $user): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $user->getProducts(), 
            'categories' => $this->productservice->getCountByCategory(),
            'users'=>$this->userRepository->findAll()
        ]);
    }

}

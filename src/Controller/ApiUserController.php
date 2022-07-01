<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/api/user")
 */
class ApiUserController extends AbstractController
{
    /**
     * @Route("/", name="api_user_index", format="json", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
       
        $serializer = new Serializer ([new ObjectNormalizer()]);
       foreach ($userRepository->findAll() as $user) {
           $users[] = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['name','age', 'phone']]); // au lieu de le faire manuelle $users[]= ['name'=> $user->gatName(),...] if faut convertir arraylist(resultat find all) to array et object user to array cepourcela on utilise serialization
       }
       
        return new JsonResponse($users);
    }

     /**
     * @Route("/new", name="api_user_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user); // liez l'objet form a la requette reçu
        $form->handleRequest($request);

        $form->submit($request->request->all());
        $serializer = new Serializer([new ObjectNormalizer()]);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            /***/ 
            $user_json = $serializer->normalize($user, null, [AbstractNormalizer::ATTRIBUTES => ['name','age', 'phone']]); // au lieu de le faire manuelle $users[]= ['name'=> $user->gatName(),...] if faut convertir arraylist(resultat find all) to array et object user to array cepourcela on utilise serialization
        

            return new JsonResponse($user_json);
        }

        return new JsonResponse($serializer->normalize($form->getErrors()));
        
    }
     /**
     * @Route("/{id}/edit", name="api_user_edit", methods={"GET","POST"})
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
        ]);
    }
    /**
     * @Route("/{id}", name="api_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
      /**
     * @Route("/{id}", name="api_user_delete", methods={"POST"})
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
<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/back/user/browse", name="back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/back/user/add", name="back_user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // hash du password
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $user->getPassword());
            // On le remet dans $user
            $user->setPassword($hashedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/user/read/{id}", name="back_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/back/user/edit/{id<\d+>}", name="back_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // le form va transmettre les données de la requête à l'entité qui est non mappé
        // Donc on modifie le mot de passe nous meme plus bas si besoin
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // hashage du mot de passe seulement si on change le mot de passe
            // on va chercher dans le formulaire, la valeur du password
            // Si le mot de passe du form n'est pas vide
            // c'est qu'on veut le changer !
            if (!empty($form->get('password')->getData())) {
                // C'est là qu'on encode le mot de passe du User (qui se trouve dans $user)
                $hashedPassword = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
                // On réassigne le mot passe encodé dans le User
                $user->setPassword($hashedPassword);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/back/user/delete/{id<\d+>}", name="back_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

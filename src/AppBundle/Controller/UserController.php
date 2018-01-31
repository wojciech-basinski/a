<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserEditType;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_all")
     */
    public function showUsersAction()
    {
        $userService = $this->get('users');
        $users = $userService->getAllUsers();

        return $this->render('users/all.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/edit/{id}", name="users_edit")
     */
    public function editUserAction(Request $request, int $id)
    {
        $originalUser = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $user = clone $originalUser;
        if (!$user) {
            $this->addFlash('error','Nie znaleziono użytkownika.');
            return $this->redirectToRoute('users_all');
        }

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->get('users')->editUser($originalUser, $user)) {
                return $this->redirectToRoute('users_all');
            }
        }

        return $this->render('users/add.html.twig', [
            'form' => $form->createView()
        ]);

    }
    
    /**
     * @Route("/users/delete/{id}", name="users_delete")
     */
    public function deleteUserAction(int $id)
    {
        $userService = $this->get('users');
        $userService->deleteUser($id, $this->getUser()->getId());

        return $this->render('users/delete.html.twig');
    }
    /**
     * @Route("/users/add", name="users_add")
     */
    public function addUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('users')->register($user);

            $this->addFlash('success','Pomyślnie dodano użytkownika.');
            return $this->redirectToRoute('users_all');
        }

        return $this->render('users/add.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $doctrine;
    private $em;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $doctrine)

    {
        $this->em = $em;
        $this->doctrine = $doctrine;
    }


    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {

        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('redirect_nonAuthorised');
        }
        return $this->render('user/list.html.twig', ['users' => $this->doctrine->getRepository(User::class)->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createAction(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $admin = $this->getUser();
        if (!empty($admin)) {

            if (in_array('ROLE_ADMIN', $admin->getRoles())) {
                $user = new User();
                $form = $this->createForm(UserType::class, $user);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {


                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $user->getPassword()
                    );
                    $user->setPassword($hashedPassword);


                    $this->em->persist($user);
                    $this->em->flush();

                    $this->addFlash('success', "L'utilisateur a bien été ajouté.");

                    return $this->redirectToRoute('user_list');
                }
            } else {
                return $this->redirectToRoute('redirect_nonAuthorised');
            }
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}

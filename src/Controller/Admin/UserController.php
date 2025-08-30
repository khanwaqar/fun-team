<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends BaseAdminController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/user/index.html.twig', compact('users'));
    }

    #[Route('/create', name: 'admin_user_create')]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));

            $plainPassword = $request->request->get('password', 'default123');
            $hashed = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashed);

            $user->setRoles([$request->request->get('role', 'ROLE_USER')]);
            $user->setDob(new \DateTime($request->request->get('dob')));
            $user->setJoinedAt(new \DateTime());

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User created successfully.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/create.html.twig');
    }
}

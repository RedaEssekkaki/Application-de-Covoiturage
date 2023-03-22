<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(UserInterface $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}

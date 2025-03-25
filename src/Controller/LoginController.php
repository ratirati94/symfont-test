<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('login/index.html.twig');
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {}
}


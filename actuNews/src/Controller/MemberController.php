<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{

    /**
     * @Route("/login", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        return $this->render('member/login.html.twig');
    }

    /**
     * @Route("/register", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register()
    {
        return $this->render('member/register.html.twig');
    }

}
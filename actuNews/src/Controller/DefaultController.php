<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController

{
    public function index()
    {
        return $this->render('default/index.html.twig');
        #return new Response('<h1>ACCUEIL</h1>');
    }

    /**
     * @Route("/category/{alias}", name="default_category", methods={"GET"})
     * @return Response
     */
    public function category($alias)
    {
        return $this->render('default/category.html.twig', ['articles'=>[]]);
        #return new Response("<h1>CATEGORIE : $alias</h1>");
    }

    /**
     * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
     * @return Response
     */
    public function article($category,$alias ,$id)
    {
        return $this->render('default/article.html.twig');
        #return new Response("<h1>ARTICLE</h1>");
    }
}
<?php
namespace App\Controller;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController
{
    public function index()
    {
        #Récupération de tous les articles
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();
        return $this->render('default/index.html.twig',[
            'articles' => $articles
        ]);
        #return new Response('<h1>ACCUEIL</h1>');
    }
    /**
     * @Route("/category/{alias}", name="default_category", methods={"GET"})
     * @return Response
     */
//    public function category(Category $category)
//    {
//        return $this->render('default/category.html.twig',
//            ['articles'=>$category->getArticles(),
//                'category'=>$category
//            ]);
//        #return new Response("<h1>CATEGORIE : $alias</h1>");
//    }
    public function category($alias)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['alias'=>$alias]);
        $articles = $category->getArticles();
        return $this->render('default/category.html.twig',
            ['articles'=>$category->getArticles(),
                'category'=>$category]);
        #return new Response("<h1>CATEGORIE : $alias</h1>");
    }
    /**
     * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
     *@param Article $article
     * @return Response
     */
    public function article( Article $article)
    {
        #Récupération de l'article
//        $article = $this->getDoctrine()
//            ->getRepository(Article::class)
//            ->find($id); avec cette méthode on passe article($id)
        #dump($article);
        return $this->render('default/article.html.twig', ['article' => $article]);
        #return new Response("<h1>ARTICLE</h1>");
    }
    public function menu()
    {
        #Récupération des categories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        #transmission à la vue
        return $this->render('components/_nav.html.twig', [
            'categories' => $categories
        ]);
    }
    public function footerMenu()
    {
        #Récupération des categories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        #transmission à la vue
        return $this->render('components/footer.html.twig', [
            'categories' => $categories
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * Démonstration de l'ajout d'un article avec Doctrine
     * @Route("/demo/article", name="article_demo")
     */
    public function demo()
    {
        #Création d'une catégorie
        $category= new Category();
        $category->setName('Economie')
                 ->setAlias('economie');

        #Création d'un utilsateur (journaliste)
        $user= new User();
        $user->setFirstname('Astrid')
             ->setLastname('JONATHAN')
             ->setEmail('astrid@actu.news')
             ->setPassword('1234')
             ->setRoles(['ROLE_JOURNALISTE']);

        #Création d'un article
        $article = new Article();
        $article->setTitle('Transformation numérique')
            ->setAlias('transformation-numerique')
            ->setContent('<h3>Pour Marcien Amoungui, le directeur de la Best Practice Transformation Digitale chez Umanis, la locution « transformation digitale » est devenue une expression valise qui embarque un peu tout ce que les gens veulent y mettre. Pourtant elle a une définition assez précise : « La digitalisation va toucher plusieurs métiers autour de leur processus et apporter une valeur par le décloisonnement qui va générer un impact fort en direction des collaborateurs, des clients et de l’ensemble des partenaires et de l’écosystème de l’entreprise. La transformation numérique a vocation à améliorer les processus dans l’ensemble des processus ; autour de la stratégie, de la mise sur le marché, de l’accélération de sortie de nouveaux produits et services, et à améliorer et fidéliser les clients et collaborateurs. La digitalisation est une vision offensive face à la concurrence. »</h3>')
            ->setImage('inf180_transfo_01.jpg')
            ->setCategory($category)
            ->setUser($user);

        /**
         * Récupération du Manager de Doctrine
         * ------------------------------------
         * Entity Manager est une classe qui
         * sais comment persister d'autres
         * classes.( Effectuer des opéarations
         * CRUD sur nos entités)
         * -------------------------------------
         * Ici, doctrine va s'aider des annotations
         * pour gérer nos données.
         */

        $em=$this->getDoctrine()->getManager();
        #On précise ce que l'on souhaite sauvegarder
        $em->persist($category);
        $em->persist($user);
        $em->persist($article);
        #On déclenche de l'exécution par doctrine
        $em->flush();
        #On retourne une réponse
        return new Response('Nouvel article: '. $article->getTitle());
    }
}

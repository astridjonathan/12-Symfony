<?php
namespace App\Controller;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use http\Client\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;
class ArticleController extends AbstractController
{
    use HelperTrait;
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
    /**
     * Formulaire permettant l'ajout d'un article
     * @Route("/creer-un-article", name="article_add")
     * @Security("is_granted('ROLE_REPORTER')")
     * @return Response
     */
    public function addArticle(\Symfony\Component\HttpFoundation\Request $request)
    {
        #Création d'un nouvel article
        $article= new Article();
        #Récupérer un user en attendant user connecté
        $journaliste = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(2);
        #On affecte le User à l'article
        $article->setUser($journaliste);
        #Création d'un formulaire
        $form = $this->createFormBuilder($article)
            #Titre de l'article
            ->add('title', TextType::class,[
                'required' =>true, #par defaut à true pas à mettre
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre de l\'article'
                ]
            ])
            #Category
            ->add('category', EntityType::class,[
                'class'=> Category::class,
                'choice_label' => 'name',
                'label' => false
            ])
            #Article's content
            ->add('content', TextareaType::class, [
                'required'=>false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Contenu de l\'article'
                ]
            ])
            #Image upload
            ->add('image', FileType::class,[
                'label' => false,
                'attr' => [
                    'class' => 'dropify',
                    'placeholder' => 'Télécharger une image '
                ]
            ])
            #Bouton envoyer
            ->add('submit', SubmitType::class,[
                'label' => 'Publier mon Article'
            ])
            #Creates Form
            ->getForm();
        #Pemet à SF de gérer les données réçues
        $form->handleRequest($request);
        #Si le formulaire est soumis et que c'est validé
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $newFilename = $this->slugify($article->getTitle()) . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('articles_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setImage($newFilename);
            } #fin upload image
            #Génération de l'alias de l'article
            $article->setAlias($this->slugify($article->getTitle()));
            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            #Notification flash
            $this->addFlash('notice', 'Félicitations votre article est en ligne !');
            #Redirection
            return $this->redirectToRoute('default_article',[
                'category' => $article->getCategory()->getAlias(),
                'alias'=> $article->getAlias(),
                'id'=>$article->getId()
            ]);
        }
        #Transmission du formulaire à la vue
        return $this->render('article/form.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
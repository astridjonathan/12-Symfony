<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/connexion.html",name="user_login", methods={"GET / POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        return $this->render('user/login.html.twig');
    } #FIn pubf login

    /**
     * @Route("/inscription.html",name="user_register", methods={"GET / POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        #1. Création d'un nouvel utilisateur
        $user = new User();
        #2. Création du formulaire
        $formUser = $this->createFormBuilder($user)
            #Firstname
            ->add('firstname',TextType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            #Lastname
            ->add('lastname',TextType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            #Email
            ->add('email',EmailType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Votre email'
                ]
            ])
            #Password
            ->add('password',PasswordType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Mot de passe'
                ]
            ])
            #Roles
            ->add('roles',TextType::class,[
                'label' => false,

                'attr' => [
                    'value' => 'Journaliste',
                    'disable'=>true,
                ]
            ])
            #Bouton envoyer
            ->add('submit', SubmitType::class,[
            'label' => 'S\'inscrire'
            ])
            #Creates Form
            ->getForm();

        #3. Vérification de la soumission
        $formUser->handleRequest($request);
        #4. Encodage du MDP(ignorer cette étape)

        #5. Sauvegarde en BDD
        if ($formUser->isSubmitted() && $formUser->isValid()) {


            #Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            #6. Notification flash
            $this->addFlash('notice', 'Félicitations votre inscription est prise en compte !');

            #7. Redirection sur la page de connexion
            return $this->redirectToRoute('user_login',[
                'id'=>$user->getId()
            ]);
        }

        #Transmission du formulaire à la vue
        return $this->render('user/register.html.twig',[
            'form' => $formUser->createView()
        ]);


    } #FIn pubf register

}
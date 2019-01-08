<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
    * @Route("/user/infos", name="userInfo")
    */
    public function showConnectedUser(Request $request, FileUploader $fileuploader){
        //pour restreindre l'accès au contrôleur aux seuls utilisateurs connectés
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //récupérer l'utilisateur connecté
        $user = $this->getUser();

        //traitement du formulaire d'upload d'image
        if($request->files->get('image')){
            //on m'a envoyé une image
            $filename = $fileuploader->upload($request->files->get('image'), $this->getParameter('user_image_directory'), $user->getImage());
            //j'injecte le nom du fichier dans la propriété image
            $user->setImage($filename);

            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->flush();
        }

        
        dump($user);

        return $this->render('security/user.html.twig', ['moi' => $user ]);
    }
}

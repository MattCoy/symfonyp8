<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\ArticleAdminType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
    * @Route("/test/deny")
    * Pour contrôler plus finement l'accès à nos contrôleurs
    */
    public function testDeny(){
    	//si l'utilisateur n'a pas le ROLE_AUTEUR, une erreur 403 est renvoyée
    	$this->denyAccessUnlessGranted('ROLE_AUTEUR', null, 'page interdite!');

    	//si on a le ROLE _AUTEUR, le reste du controleur est exécuté

    	return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController'
        ]);

    }

    /**
    * Autre méthode pour restreindre l'accès à un contrôleur : les annotations
    * @Route("test/deny2")
    * @Security("has_role('ROLE_AUTEUR')")
    */
    public function testDeny2(){

    	return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController'
        ]);

    }

    /**
    * @Route("admin/article/add", name="addArticleAdmin")
    *
    */
    public function addArticle(Request $request)
    {

        $form = $this->createForm(ArticleAdminType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            $entitymanager = $this->getDoctrine()->getManager();

            $entitymanager->persist($article);

            $entitymanager->flush();

        }

        return $this->render('admin/add.article.html.twig', ['form' => $form->createView()]);
    }

    /**
    * @Route("admin/article/update/{id}", name="updateArticleAdmin", requirements={"id"="\d+"})
    */
    public function updateArticle(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleAdminType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article = $form->getData();

            $entitymanager = $this->getDoctrine()->getManager();

            $entitymanager->flush();
        }
        return $this->render('admin/add.article.html.twig', ['form' => $form->createView()]);
    }
}

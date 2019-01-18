<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Service\JsonArticleGenerator;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/auteur/", name="auteur1")
     */
    public function index(Request $request)
    {
    	$idAuteur = $request->request->get('idAuteur', null);

    	if(empty($idAuteur) || !preg_match("#^\d+$#", $idAuteur)){
    		return new Response('paramètre invalide');
    	}

    	$user = $this->getDoctrine()
    				->getRepository(User::class)
    				->findById($idAuteur);

    	$articles = $this->getDoctrine()
    					->getRepository(Article::class)
    					->findByUser($user);
        return $this->render('ajax/articles.html.twig', ['articles'=>$articles]);
    }

    /**
    * @Route("/ajax/auteur2/{id}", name="auteur2", requirements={"id"="\d+"})
    */
    public function auteur2(User $user, JsonArticleGenerator $jsongenerator){

    	$articles = $this->getDoctrine()
    					->getRepository(Article::class)
    					->findByUser($user);
                        
    	$result = $jsongenerator->getArticles($articles);
    	
    	//renvoi d'une réponse au format json
    	return $this->json(['status' => 'ok', 'articles' => $result]);

    }

    /**
    * @Route("/ajax/categorie/{id}", name="ajaxCategorie", requirements={"id"="\d+"})
    */
    public function ajax3(Categorie $categorie, JsonArticleGenerator $jsongenerator){

    	$articles = $this->getDoctrine()
    					->getRepository(Article::class)
    					->findByCategorie($categorie);

    	$result = $jsongenerator->getArticles($articles);

    	//renvoi d'une réponse au format json
    	return $this->json(['status' => 'ok', 'articles' => $result]);

    }
}

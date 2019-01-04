<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleUserType;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    /**
     *Route qui va afficher la liste des articles
     * @Route("/articles", name="articles")
     */
    public function index()
    {

        //récupération de la liste des articles
        // $articleDB = new ArticleDB();
        //$articles = $articleDB->findAll()
        $repository = $this->getDoctrine()->getRepository(Article::class);
        //utilisation de la méthode custom qui fait une jointure
        $articles = $repository->myFindAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
    *@Route("/article/add", name="addArticle")
    */
    public function addArticle(Request $request){

        //seul un utilisateur connecté peut ajouter un article
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    	//pour pouvoir sauvegarder un objet = insérer les infos dans la table, on utilise l'entity manager
    	$entityManager = $this->getDoctrine()->getManager();
    	
        $form = $this->createForm(ArticleUserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();
            //l'auteur de l'article est l'utilisateur connecté
            $article->setUser($this->getUser());
            //je fixe la date de publication de l'article
            $article->setDatePubli(new \DateTime(date('Y-m-d H:i:s')));

            $entityManager->persist($article);

            $entityManager->flush();

            $this->addFlash('success', 'article ajouté');

            return $this->redirectToRoute('articles');

        }

    	return $this->render('article/add.html.twig', ['form' => $form->createView()]);

    }

    /*Créer une page qui va afficher les détails d'un article.
    On utilise l'id de l'article pour récupérer l'article (placeholder dans l'url)*/
    /**
    *@Route("/article/{id}", name="showArticle", requirements={"id"="\d+"})
    */
    public function showArticle($id){

        $repository = $this->getDoctrine()->getRepository(Article::class);
        $article = $repository->find($id);

        //génération d'une erreur si aucun article n'est trouvé
        if(!$article){
            throw $this->createNotFoundException('No article found');
        }

        return $this->render('article/article.html.twig',
                                        ['article' => $article]
        );

    }

    /**
    *@Route("/article/recent", name="showRecentArticles")
    */
    public function showRecent(){

        $repository = $this->getDoctrine()->getRepository(Article::class);
        //requête SQL :  articles est un tableau de tableaux
        $articles = $repository->findAllPostedAfter('2000-01-01');
        //requête objet :articles2 est un tableau d'objets
        $articles2 = $repository->findAllPostedAfter2('2000-01-01');

        return $this->render('article/recent.html.twig', 
            ['articles'=>$articles, 'articles2' => $articles2]);

    }

    /**
    *@Route("article/update/{id}", name="updateArticle", requirements={"id"="\d+"})
    */
    public function updateArticle(Request $request, Article $article){

        //pour pouvoir sauvegarder un objet = insérer les infos dans la table, on utilise l'entity manager
        $entityManager = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            $entityManager->flush();

            $this->addFlash('success', 'article modifié');

            return $this->redirectToRoute('articles');

        }

        return $this->render('article/add.html.twig', ['form' => $form->createView()]);
    }

    /**
    *@Route("/article/delete/{id}", name="deleteArticle", requirements={"id"="\d+"})
    *Le param converter : on explique à Symfony que l'on veut convertir directement l'id en objet de classe Article en mettant le nom de la classe dans les parenthèses
    */
    public function deleteArticle(Article $article){

        //récupération de l'entity manager, nécessaire pour la suppression
       $entityManager = $this->getDoctrine()->getManager();
       //je veux supprimer cet article
       $entityManager->remove($article);
       //pour valider la suppression
       $entityManager->flush();

       //génération d'un message flash
       $this->addFlash('warning', 'Article supprimé');
       //redirection vers la liste des articles
       return $this->redirectToRoute('articles');
    }

}

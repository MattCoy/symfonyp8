<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\FileUploader;
use App\Form\CommentType;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function addArticle(Request $request, FileUploader $fileuploader){

        //seul un utilisateur connecté peut ajouter un article
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    	//pour pouvoir sauvegarder un objet = insérer les infos dans la table, on utilise l'entity manager
    	$entityManager = $this->getDoctrine()->getManager();
    	
        $form = $this->createForm(ArticleUserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            //$article->getImage() contient un objet qui représent le fichier image envoyé
            $file = $article->getImage();

            $filename = $file ? $fileuploader->upload($file, $this->getParameter('article_image_directory')) : '';

            //je remplace l'attribut imgae qui contient toujours le fichier par le nom du fichier
            $article->setImage($filename);

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
    public function showArticle(Article $article, Request $request){

        //génération d'une erreur si aucun article n'est trouvé
        if(!$article){
            throw $this->createNotFoundException('No article found');
        }

        $commentForm = $this->createForm(CommentType::class);
        //si on veut restreindre l'ajout de commentaire aux utilisateurs connectés
        $user = $this->getUser();
        if($user instanceof UserInterface){            

            $commentForm->handleRequest($request);

            if($commentForm->isSubmitted() && $commentForm->isValid()){
                $comment = $commentForm->getData();
                //je dois alors préciser qui est l'auteur du commentaire
                $comment->setUser($this->getUser());
                //je décide de l'article auqel il est lié
                $comment->setArticle($article);
                //je fixe la date de publi
                $comment->setDatePubli(new \DateTime(date('Y-m-d H:i:s')));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
            }

        }
        

        return $this->render('article/article.html.twig',
                            [
                                'article' => $article,
                                'commentForm' => $commentForm->createView()
                            ]
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
    public function updateArticle(Request $request, Article $article, FileUploader $fileuploader){

        $this->denyAccessUnlessGranted('edit', $article, 'Vous ne pouvez pas modifier cet article');

        //je stocke le nom du fichier image
        $filename = $article->getImage();

        //on remplace le nom du fichier par un objet de classe File
        //pour pouvoir générer le formulaire
        if($article->getImage()){
            $article->setImage(new File($this->getParameter('upload_directory') . $this->getParameter('article_image_directory') . '/' . $filename ));
        }

        //pour pouvoir sauvegarder un objet = insérer les infos dans la table, on utilise l'entity manager
        $entityManager = $this->getDoctrine()->getManager();
        
        $form = $this->createForm(ArticleUserType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();

            //je ne fais le traitement que si une image a été envoyée
            if($article->getImage()){
                //je récupère le fichier
                $file = $article->getImage();

                $filename = $fileuploader->upload($file, $this->getParameter('article_image_directory'), $filename);
            }

            $article->setImage($filename);

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

        $this->denyAccessUnlessGranted('delete', $article, 'Vous ne pouvez pas supprimer cet article');

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

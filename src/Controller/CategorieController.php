<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategorieType;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categories", name="categories")
     */
    public function index()
    {

    	//rcupération du repository de la classe Categorie
    	$repository = $this->getDoctrine()->getRepository(Categorie::class);

    	//récupérations des categories
    	$categories = $repository->findAll();

    	//je passe les catégories en paramètre à ma vue
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
    *@Route("/categorie/add", name="addCategorie")
    * $request contient toutes les informations sur la requête http, notamment GET et POST
    */
    public function addCategorie(Request $request){
    	//le manager va me permettre de persister mon entité
    	$entityManager = $this->getDoctrine()->getManager();

    	//je crée un objet Categorie
    	$categorie = new Categorie();

    	//je crée un objet formulaire de classe CategorieType
    	$form = $this->createForm(CategorieType::class);

    	//je demande au formulaire de gérer la requête
    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()){

    		//le formulaire a été soumis et validé

    		//je crée un objet categorie à partir des données envoyées
    		//$form->getData() contient les données envoyées par l'utilisateur
    		$categorie = $form->getData();
    		//je rentre la date de création
    		$categorie->setDateCreation(new \DateTime(date('Y-m-d H:i:s')));
    		//je persiste ma catégorie
    		$entityManager->persist($categorie);
    		$entityManager->flush();

    		$this->addFlash('success', 'catégorie ajoutée!');
    		return $this->redirectToRoute('categories');

    	}    	

    	//je passe mon formulaire en paramètre de ma vue
    	return $this->render('categorie/add.html.twig', ['form' => $form->createView()]);

    }

    /**
    *@Route("/categories/recentes", name="categoriesRecentes")
    */
    public function getLastFive(){

    	$repository = $this->getDoctrine()->getRepository(Categorie::class);

    	$categories = $repository->getLastFive();

    	return $this->render('categorie/recentes.html.twig', [
            'categories' => $categories
        ]);

    }

    /**
    *@Route("/categorie/update/{id}", name="updateCategorie", requirements={"id"="\d+"})
    */
    public function updateCategorie(Request $request, Categorie $categorie){

    	//récupération du manager
    	$entityManager = $this->getDoctrine()->getManager();
    	
    	//je crée mon formulaire, je lui passe en second paramètre mon objet catégorie afin qu'il pré-remplisse le formulaire
    	$form = $this->createForm(CategorieType::class, $categorie);
    	//je lui donne la requête
    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()){
    		//si le formulaire a été envoyé et validé

    		//on récupère l'objet catégorie
    		$categorie = $form->getData();

    		//enregistrement dans la base
    		$entityManager->flush();

    		$this->addFlash('success', 'catégorie modifiée');
    		return $this->redirectToRoute('categories');
    	}

    	return $this->render('categorie/add.html.twig', ['form' => $form->createView()]);

    }

    /**
    *@Route("/categorie/delete/{id}", name="deleteCategorie", requirements={"id"="\d+"})
    */
    public function deleteCategorie(Categorie $categorie){

    	//récupération du manager
    	$entityManager = $this->getDoctrine()->getManager();
    	//modification en dur
    	$entityManager->remove($categorie);
    	//exécution des requêtes sql
    	$entityManager->flush();

    	$this->addFlash('warning', 'catégorie supprimée');

    	return $this->redirectToRoute('categories');

    }

    /**
    * @Route("/categorie/{id}", name="categorie", requirements={"id"="\d+"})
    */
    public function show(Categorie $categorie)
    {
        return $this->render('categorie/show.html.twig', ['categorie' => $categorie]);
    }

}

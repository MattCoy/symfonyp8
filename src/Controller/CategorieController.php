<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;

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
    */
    public function addCategorie(){
    	//le manager va me permettre de persister mon entité
    	$entityManager = $this->getDoctrine()->getManager();

    	//je crée un objet Categorie
    	$categorie = new Categorie();
    	//je le rempli en dur pour l'instant
    	$categorie->setLibelle('science');
    	$categorie->setDescription("klhmklhjmkhj");
    	$categorie->setDateCreation(new \DateTime(date('Y-m-d H:i:s')));

    	$entityManager->persist($categorie);

    	$entityManager->flush();

    	$this->addFlash('success', 'catégorie ajoutée');

    	return $this->redirectToRoute('categories');

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
    public function updateCategorie(Categorie $categorie){

    	//récupération du manager
    	$entityManager = $this->getDoctrine()->getManager();
    	//modification en dur
    	$categorie->setLibelle('sport');
    	//exécution des requêtes sql
    	$entityManager->flush();

    	$this->addFlash('success', 'catégorie modifiée');

    	return $this->redirectToRoute('categories');

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

}

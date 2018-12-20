<?php
//je range toutes mes classes de controleurs dans le namespace App\Controller
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
//pour pouvoir utiliser les annotations:
use Symfony\Component\Routing\Annotation\Route;

class HomeController{


	//déclaration de notre méthode / controleur
	/**
	*grâce aux annotations, je peux déclarer ma route
	*@Route("/bonjour")
	*
	*/
	public function bonjour(){

		return new Response('<html><body><strong>Bonjour</strong></body><html>');

	}

	//Créer une page  pour l'url /exercice1/comment-allez-vous, qui affiche "bien, merci"
	/**
	*je définit la route pour ce controleur
	*@Route("/exercice1/comment-allez-vous")
	*/
	public function cava(){
		return new Response('bien merci!');
	}

}
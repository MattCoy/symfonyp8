<?php
//je range toutes mes classes de controleurs dans le namespace App\Controller
namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
//pour pouvoir utiliser les annotations:
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//pour pouvoir utiliser la méthode render et autres méthodes utiles
//on hérite de la classe AbstractController
class HomeController extends AbstractController
{


	//déclaration de notre méthode / controleur
	/**
	*grâce aux annotations, je peux déclarer ma route
	*@Route("/bonjour", name="bonjour")
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

	/**
	*@Route("/", name="home")
	*/
	public function home(){

		$pseudo = 'Toto';
		//Symfony va chercher les vues dans /templates
		//je peux passer das variables en paramètre à ma vue twig
		//grâce à un tableau qui contient en clé, les noms des paramètres et en valeur leurs valeurs
		//sur index.html.twig la variable nom sera accessible
		return $this->render('index.html.twig', array('nom' => $pseudo));

	}

	/*Créer une page  pour l'url /exercice2/heure
		Dans le controleur, stocker la date et l'heure dans une variable
		Passer cette variable à une vue twig (par ex exercice.html.twig), pour qu'elle affiche la date
		*/
	/**
	*@Route("/exercice2/heure")
	*
	*/
	public function heure(){

		$heure = date('H\hi');
		$date = date('d/m/Y');

		return $this->render("exercice2.html.twig", array('heure' => $heure, 'date' => $date));

	}

	/**
	*on peut controler ce que va contenir le placeholder avec une regex
	*@Route("/bonjour/{nom}", name="bonjourNom", requirements={"nom"="[a-z]+"})
	*
	*/
	public function bonjourPseudo($nom){
		//$nom est automatiquement envoyé en paramètre de notre méthode et contiendra tout ce qui suit bonjour/
		return $this->render('bonjour.html.twig', array('pseudo' => $nom));

	}

	/**
	* méthode qui va faire une redirection vers la page d'accueil
	*@Route("/redirect")
	*
	*/
	public function redirectHome(){

		//pour faire une redirection :en paramètre le nom de la route vers laquelle on veut rediriger
		return $this->redirectToRoute('home');
	}

}
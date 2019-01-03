<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Categorie;
use App\Entity\Article;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //on va créer 10 catégories
    	for($i=1;$i<=10;$i++){

    		$categorie = new Categorie();
    		$categorie->setLibelle('catégorie' . $i);
    		$categorie->setDescription('description' . $i);
    		$categorie->setDateCreation(new \DateTime(date('Y-m-d H:i:s')));

    		$manager->persist($categorie);
    	}

    	//création de 30 articles
    	for ($i=1;$i<=30;$i++){

    		$article = new Article();
    		$article->setTitle('Titre' . $i);
    		$article->setContent("Un contenu vraiment très intéressant " . $i);

    		//on va générer les dates aléatoirement
    		$timestamp = mt_rand(1, time());
    		//formatage du timestamp en date
    		$randomDate = date('Y-m-d H:i:s', $timestamp);

    		$article->SetDatePubli(new \DateTime($randomDate));

    		//tableau d'auteurs dans lequel on vient piocher au hasard
    		$auteurs = ['Verlaine', 'Hugo', 'Voltaire', 'Zola', 'Dumas', 'Duras', 'Molière', 'Ribéry'];
    		//array_rand choisit au hasard une clé dans un tableau
    		$article->setAuthor($auteurs[array_rand($auteurs)]);

    		$manager->persist($article);

    	}

        $manager->flush();
    }
}

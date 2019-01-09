<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Categorie;
use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    //attribut pour stocker l'encoder
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        //lors de l'instanciation, on stocke dans l'attribut encoder, l'objet qui va nous permettre d'encoder les mdp
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //tableau qui va contenir mes objets User
        $users = [];
        //création de 5 users
        for($i=1;$i<=5;$i++){

            $user = new User();
            $user->setUsername('Toto' . $i);
            $user->setEmail('toto' . $i . '@toto.to');
            if($i=== 1){//je veux que toto1 aie le rôle Admin
                $roles = ['ROLE_USER', 'ROLE_ADMIN'];
            }
            else{
                $roles =['ROLE_USER'];
            }
            $user->setRoles($roles);

            $plainPassword = 'toto';
            $mdpencoded = $this->encoder->encodePassword($user, $plainPassword);
            $user->setPassword($mdpencoded);
            $user->setImage('');

            $manager->persist($user);

            //je rempli mon tableau users
            $users[] = $user;
        }

        $categories = [];
        //on va créer 10 catégories
    	for($i=1;$i<=10;$i++){

    		$categorie = new Categorie();
    		$categorie->setLibelle('catégorie' . $i);
    		$categorie->setDescription('description' . $i);
    		$categorie->setDateCreation(new \DateTime(date('Y-m-d H:i:s')));

    		$manager->persist($categorie);
            $categories[] = $categorie;
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

    		$article->setDatePubli(new \DateTime($randomDate));

    		//array_rand choisit au hasard une clé dans un tableau
    		$article->setUser($users[array_rand($users)]);

            $article->setCategorie($categories[array_rand($categories)]);
            $article->setImage('');

    		$manager->persist($article);

    	}

        $manager->flush();
    }
}

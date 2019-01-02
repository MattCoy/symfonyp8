<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /*
    * Méthode qui va récupérer les articles dont la date de publication est plus récente 
    * que la date donnée en paramètre 
    * On peut faire cette requête en SQL "à l'ancienne" 
    * @param $date_post string, la date au format datetime 
    * @return array of arrays of article data 
    */ 
    public function findAllPostedAfter($datePost){

        //on récupère l'équivalent de l'objet de connexion à pdo que l'on utilisait
        $connexion = $this->getEntityManager()->getConnection();
        //on stocke la requête dans une variable
        $sql = '
            SELECT * FROM article 
            WHERE date_publi > :datePost 
            ORDER BY date_publi DESC
        ';
        $select = $connexion->prepare($sql);
        $select->bindValue(':datePost', $datePost);
        $select->execute();

        //je renvoie un tableau de tableaux d'articles
        return $select->fetchAll();

    }

    /* 
    * Cette Méthode fait la même chose que findAllPostedAfter() 
    * mais on fait la requête en objet 
    * @param $date_post string, la date au format datetime 
    * @return array of article objects 
    */
    public function findAllPostedAfter2($datePost){

        $queryBuilder = $this->createQueryBuilder('a')
            ->andWhere('a.date_publi > :datePost')
            ->setParameter('datePost', $datePost)
            ->orderBy('a.date_publi', 'DESC')
            ->getQuery();

        return $queryBuilder->execute();

    }
}

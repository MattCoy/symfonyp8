<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class ArticleVoter extends Voter
{

    private $security;

    const EDIT = 'edit';
    const DELETE = 'delete';

    public function __construct(Security $security){
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        //vérif du role
        if($this->security->isGranted('ROLE_ADMIN')){
            //si l'utilisateur connecté a le role admin, on revoie true tout de suite
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                //l'auteur de l'article peut le modifier
                if($user === $subject->getUser()){
                    return true;
                }
                else{
                    return false;
                }
                break;
            case self::DELETE:
                //l'auteur de l'article peut le supprimer
                return $user === $subject->getUser();
                break;
        }

        return false;
    }
}

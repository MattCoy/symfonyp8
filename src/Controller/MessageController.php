<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MessageType;

class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function index()
    {
    	//récupération du répository de l'entité message
    	$repository = $this->getDoctrine()->getRepository(Message::class);
    	//récupération des messages (sous la forme d'un tableau d'objets)
    	$messages = $repository->findAll();
    	//je génère la vue en lui passant en paramètre $message
        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
    * @Route("/message/add", name="addmessage")
    */
    public function addMessage(Request $request){

    	$message = new Message();

    	//création du formulaire
    	$form = $this->createForm(MessageType::class);

    	$form->handleRequest($request);

    	//traitement du formulaire
    	if($form->isSubmitted() && $form->isValid()){

    		$message= $form->getData();
    		// je génère manuellement la date d'envoi
    		$message->setDateEnvoi(New \DateTime(date('Y-m-d H:i:s')));
    		//récupération de l'entity manager
    		$entityManager = $this->getDoctrine()->getManager();
    		//je persist mon objet message
    		$entityManager->persist($message);
    		//j'exécute les requêtes sql
    		$entityManager->flush();
    		//création d'un message de confirmation
    		$this->addFlash('success', 'message envoyé');
    		//redirection vers la fiche du message que l'on vient de créer
    		return $this->redirectToRoute('message', ['id'=>$message->getId()]);

    	}

    	return $this->render("message/add.html.twig", ['form' => $form->createView()]);

    }

    /**
    * @Route("/message/{id}", name="message", requirements={"id"="\d+"})
    */
    public function showMessage(Message $message){

    	return $this->render("message/show.html.twig", ["message" => $message]);

    }
}

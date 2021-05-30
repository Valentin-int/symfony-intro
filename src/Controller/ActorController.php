<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actor;
use App\Entity\Program;


/**
 * @Route("/actor", name="actor_")
 */
class ActorController extends AbstractController
{

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="show")
     */
    public function show(Actor $actor): Response
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'Il n\'y à pas d\'acteur avec l\'id : ' . $actor . ' dans la table actor'
            );
        }

        return $this->render('actor/show.html.twig', [
            'actor' => $actor
        ]);
    }
}

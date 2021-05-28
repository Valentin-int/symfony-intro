<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;

class ProgramController extends AbstractController
{
    /**
     * @Route("/program/", name="program_index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render('program/index.html.twig', [
            'website' => 'Wild Séries',
            'programs' => $programs
        ]);
    }

    /**
     * @Route("/program/show/{id}", requirements={"id"="\d+"}, methods={"GET"}, name="program_show")
     */
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findOneBy(['id' => $id]);

        $seasons = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findBy(['program' => $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'Il n\'y à pas de program avec l\'id : '.$id.' dans la table program'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' =>$seasons]);
    }

    /**
    * @Route("/program/{programId}/seasons/{seasonId}", requirements={"id"="\d+"}, methods={"GET"}, name="program_season_show")
    */
    public function showSeason(int $programId, int $seasonId): Response
    {
        $program = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findOneBy(['id' => $programId]);

        $season = $this->getDoctrine()
        ->getRepository(Season::class)
        ->findOneBy(['id' => $seasonId]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season]);
    }
}

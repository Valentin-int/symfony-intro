<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Service\Slugify;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
     * @Route("/program/new", name="program_new")
     */
    public function new(Request $request,  Slugify $slugify, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($program);
            // Flush the persisted object
            $program->setOwner($this->getUser());
            $entityManager->flush();
            // Finally redirect to categories list
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);
            return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/program/show/{program_slug}", methods={"GET"}, name="program_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     */
    public function show(Program $program): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'Il n\'y à pas de program avec l\'id : ' . $program . ' dans la table program'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/program/{program_slug}/seasons/{season_id}", methods={"GET"}, name="program_season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     */
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }

    /**
     * @Route("/program/{program_slug}/seasons/{season_id}/episode/{episode_slug}", methods={"GET","POST"}, name="program_episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"season_id": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function showEpisode(Request $request, Program $program, Season $season, Episode $episode): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $comment->setEpisode($episode);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $comment->setOwner($this->getUser());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirect($request->getUri());
            }
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            "form" => $form->createView(),
            "comment" => $comment,
        ]);
    }

    /**
     * @Route("program/{edit_slug}/edit", name="program_edit", methods={"GET","POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"edit_slug": "slug"}})
     */
    public function edit(Request $request, Program $program, Slugify $slugify): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/comment/{comment_id}", name="comment_delete", methods={"GET","POST"})
     * @ParamConverter("comment", class="App\Entity\Comment", options={"mapping": {"comment_id": "id"}})
     */
    public function delete(Request $request, Comment $comment): Response
    {

        if ($this->getUser() == $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => "admin@monsite.com"]) || $this->getUser() == $comment->getOwner()) {
            if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($comment);
                $entityManager->flush();
            }
        } else {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can delete the comment!');
        }

        return $this->redirectToRoute('program_episode_show', [
            'program_slug' => $comment->getEpisode()->getSeason()->getProgram()->getSlug(),
            'season_id' => $comment->getEpisode()->getSeason()->getId(),
            'episode_slug' => $comment->getEpisode()->getSlug(),
        ]);
    }
}

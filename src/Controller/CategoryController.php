<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Program;

class CategoryController extends AbstractController
{
    /**
    *@Route("/category/", name="category_index")
    */
    public function index(): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $category
        ]);
    }

    /**
    *@Route("/category/{categoryName}", name="category_show")
    */
    public function show(string $categoryName) : Response
    {
        $categoryName = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findOneBy(['name' => $categoryName]);

        if (!$categoryName) {
            throw $this->createNotFoundException(
                'Il n\'y à pas de category avec le nom : '.$categoryName.' dans la table category'
            );
        }

        $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findBy(['category' => $categoryName->getId()],['id' => 'DESC'],3);

        return $this->render('category/show.html.twig', ['programs' => $programs]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\CreateArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/create", name="create_article")
     */
    public function create(Request $request)
    {
        $entitymanager = $this->getDoctrine()->getManager();
        $article = new Article();

        $form = $this->createForm(CreateArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();
            $entitymanager->persist($article);
            $entitymanager->flush();
        }


        return $this->render('article/create.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/article/show", name="show_article")    // Route de la nouvelle page
     */
    public function show()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class); // On appel le gestionnaire
        $articles = $repository->findAll();                                              // On fait une requête pour récupérer tous les articles

        return $this->render('article/show.html.twig', [                            // Nouveau template
            'articles' => $articles,
        ]);
    }
}

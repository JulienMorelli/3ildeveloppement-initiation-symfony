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
}

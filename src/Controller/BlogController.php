<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Panier;



class BlogController extends AbstractController
{
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(
            ['isPublished' => true],
            ['publicationDate' => 'desc']            
        );

        return $this->render('blog/index.html.twig', ['articles' => $articles]);
    }

    public function fantaisy()
    {
    	return $this->render('blog/fantaisy.html.twig');
    }

    public function add(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setLastUpdateDate(new \DateTime());

            if ($article->getPicture() !== null) {
                $file = $form->get('picture')->getData();
                $fileName =  uniqid(). '.' .$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'), // Le dossier dans le quel le fichier va etre charger
                        $fileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $article->setPicture($fileName);
            }

            if ($article->getIsPublished()) {
                $article->setPublicationDate(new \DateTime());                
            }

            $em = $this->getDoctrine()->getManager(); // On récupère l'entity manager
            $em->persist($article); // On confie notre entité à l'entity manager (on persist l'entité)
            $em->flush(); // On execute la requete

            return $this->redirectToRoute('admin');
            
            
        }

    	return $this->render('blog/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function show(Article $article)
    {
    	return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }

    public function edit(Article $article, Request $request)
    {
        $oldPicture = $article->getPicture();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setLastUpdateDate(new \DateTime());

            if ($article->getIsPublished()) {
                $article->setPublicationDate(new \DateTime());
            }

            if ($article->getPicture() !== null && $article->getPicture() !== $oldPicture) {
                $file = $form->get('picture')->getData();
                $fileName = uniqid(). '.' .$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $article->setPicture($fileName);
            } else {
                $article->setPicture($oldPicture);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

           
            return $this->redirectToRoute('admin');
            
        }

    	return $this->render('blog/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }
    
    public function remove(int $id): Response {
 
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
         
        if($article === null) {
        $articles = $this->getDoctrine()->getManager();
        $articles->remove($article);
        $articles->flush();
        }
 
        $article = $this->getDoctrine()
                        ->getRepository(Article::class)
                        ->find($id);
        $manager = $this->getDoctrine()->getManager();
 
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('deleteArticle', 'L\'article a bien étais supprimer');
        return $this->redirectToRoute('admin');
    }

    public function admin()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(
            [],
            ['lastUpdateDate' => 'DESC']
        );

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
            'users' => $users
        ]);
    }

    public function client()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('blog/client.html.twig', ['users' => $users]);
    }

   
}
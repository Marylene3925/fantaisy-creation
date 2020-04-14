<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Panier;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;

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

    
   public function bagues()
   {
        // // on appelle la liste des bagues
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        
        //dd($article);

        return $this->render('blog/bagues.html.twig', ['category' => $category]);
    }
    
    
    public function panier( Request $request, articleRepository $articleRepository)
    {
        //j'accède a la session grace au request
        $session = $request->getSession();
        // j'ai un panier avec des article ou non
        $panier = $session->get('panier', []);
        // j'enrichie le panier avec un tableau dans lequel il y aura une photo, titre..
        $panierWithData = [];
        // je fais une boucle sur le panier et je vais a chaque fois extraire la clé'id' et ca quantitée
        foreach($panier as $id => $quantity){
            //je rajoute dans 'panierWithData une case qui serait elle meme un tableau associatif et ce tableau contiendrai une case qui serait 'article' et une case 'quantity'
            $panierWithData[] = [
                // je fais appel a la fonction articlerepository (fait des requette sur des produit), jutilise la fonction 'find' pour trouver 1 article grace a son 'id'
                'article' => $articleRepository->find($id),
                'quantity'=> $quantity
            ];
        }

        //dd($panierWithData);

        // je calcul le total des pris de me articles
        $total = 0;
        // je fait une boucle pour faire mes calcul
        foreach($panierWithData as $item) {
            //je calcul le total pour 1 article
            $totalItem = $item['article']->getTarif() * $item['quantity'];
            // je rajoute le prix de 1 article  au prix total de tous les articles
            $total += $totalItem;
        }
        
        return $this->render('blog/panier.html.twig', [
            //items est la liste de mes éléments qui corespondra a 'panierwithdata'
            'items' => $panierWithData,
            // total sera la total en entier
            'total' => $totalItem
        ]);         
    }

     
    public function addArticle($id, Request $request)
    {
         //j'accède a la session grace au request
         $session = $request->getSession();
         // je recupere dans ma session le panier
        $panier = $session->get('panier', []);
        // si j'ai déja le produit selectionner dans le panier
        if(!empty($panier[$id]))
        {
            $panier[$id]++;
        } else {
        // je rajoute le produit a l'interrieur
        $panier[$id] = 1 ;
        }
        // remettre le panier dans la session pour le sauvegarder
        $session->set('panier', $panier);

        dd($session->get('panier'));
     }

     public function removeCommande($id, Request $request){

        //j'accède a la session grace au request
        $session = $request->getSession();
        // je recupere dans ma session le panier
        $panier = $session->get('panier', []);
        // si jamais mon panier n'est pas vide tu suprime l'article souhaiter
        if(!empty($panier[$id])) {
            unset($panier[$id]);
        } 
        
        $session->set('panier', $panier);
        
        return $this->redirectToRoute("panier");

     }

    

}
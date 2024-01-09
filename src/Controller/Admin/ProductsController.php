<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]

    public function index() : Response 
    {
        return $this->render('admin/products/index.html.twig');    
    }
    #[Route('/ajout', name: 'add')]

    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger) : Response 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un nouveau produit
        $product = new Products();
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);
        // On vérifie si le formulaire est soumis ET valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            // On génère le Slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            // On arrondit le prix 
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // On stocke le produit
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            // On redirige
            //return $this->redirectToRoute('admin_products_index');
            return $this->redirectToRoute('admin_products_index');
        }


        return $this->render('admin/products/add.html.twig',[
            'productForm' => $productForm->createView()
        ]);    

        //   return $this->render('admin/products/index.html.twig', compact('productForm'));

        // compact équivaut à ['productForm' => $productForm]  
        // mais ce return ne fonctionnement pas avec render et renderForm est deprecated
    }
    #[Route('/edition/{id}', name: 'edit')]

    public function edit(Products $product) : Response 
    {
        // On vérifie si l'utilisateur peut éditer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        return $this->render('admin/products/index.html.twig');    
    }
    #[Route('/suppression/{id}', name: 'delete')]

    public function delete(Products $product) : Response 
    {
                // On vérifie si l'utilisateur peut supprimer avec le Voter
                $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig');    
    }
}

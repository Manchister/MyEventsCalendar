<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories_list")
     */
    public function indexAction(Request $request)
    {
    	$categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }
    /**
     * @Route("/categories/create", name="categories_create")
     */
    public function createAction(Request $request)
    {
    	$category = new Category;

    	$form = $this->createFormBuilder($category)
    	->add('name', TextType::class, array("attr" => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
    	->add('save', SubmitType::class, array('label' => 'Create Category', 'attr' => array('class' => 'btn btn-success')))
    	->getForm();

    	// Handle Request
    	$form->handleRequest($request);

    	// Check Submit
    	if ($form->isSubmitted() && $form->isValid()) {
    		$name = $form['name']->getData();

    		// Get current date and time
    		$now = new \DateTime('now');

    		$category->setName($name);
    		$category->setCreateDate($now);

    		// Inserting Data
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($category);
    		$em->flush();

    		$this->addFlash('notice', 'Category Saved');

    		return $this->redirectToRoute('categories_list');
    	}

    	// Render Template
        return $this->render('category/create.html.twig', [
        	'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/categories/edit/{id}", name="categories_edit")
     */
    public function editAction($id, Request $request)
    {
    	$category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

    	if (!$category) {
    		throw $this->createNotFoundException('No category found for id ' . $id);
    	}

    	$category->setName($category->getName());

    	$form = $this->createFormBuilder($category)
    	->add('name', TextType::class, array("attr" => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
    	->add('save', SubmitType::class, array('label' => 'Edit Category', 'attr' => array('class' => 'btn btn-success')))
    	->getForm();

    	// Handle REquest
    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		$name = $form['name']->getData();
    		$em = $this->getDoctrine()->getManager();
    		$category = $em->getRepository('AppBundle:Category')->find($id);
    		$category->setName($name);
    		$em->flush();
    		$this->addFlash('notice', 'Category Edited');

            return $this->redirectToRoute('categories_edit');
    	}
        return $this->render('category/edit.html.twig', [
        	'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/categories/delete/{id}", name="categories_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$category = $em->getRepository('AppBundle:Category')->find($id);

    	if (!$category) {
    		throw $this->createNotFoundException('No category found for id ' . $id);
    	}

    	$em->remove($category);
    	$em->flush();
    	$this->addFlash('notice', 'Category Deleted');

    	return $this->redirectToRoute('categories_list');

    }
}

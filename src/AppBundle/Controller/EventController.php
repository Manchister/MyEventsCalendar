<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

// use Symfony\Component\Form\Extention\Core\Type\TextareaType;

class EventController extends Controller
{
    /**
     * @Route("/events", name="events_list")
     */
    public function indexAction(Request $request)
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }
    /**
     * @Route("/events/create", name="events_create")
     */
    public function createAction(Request $request)
    {
        $event = new Event;

        $form = $this->createFormBuilder($event)
        ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('category', EntityType::class, array('class' => 'AppBundle:Category','choice_label' => 'name', 'attr' => array('class' => 'form-control')))
        ->add('details', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('day', DateTimeType::class, array('attr' => array('class' => 'form-control-day', 'style' => 'margin-bottom:20px;')))
        ->add('address', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
        ->add('save', SubmitType::class, array('label' => 'Add Event', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        // Submit
        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form['title']->getData();
            $category = $form['category']->getData();
            $details = $form['details']->getData();
            $day = $form['day']->getData();
            $address = $form['address']->getData();
            // Get Current Date And Time
            $now = new \DateTime('now');

            $event->setTitle($title);
            $event->setDetails($details);
            $event->setDay($day);
            $event->setAddress($address);
            $event->setCategory($category);
            $event->setCreateDate($now);

            // Inserting Data
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash('notice', 'Event Saved');

            return $this->redirectToRoute('events_list');

        }

        // Render Template
        return $this->render('event/create.html.twig', ['form' => $form->createView()]);
    }
    /**
     * @Route("/events/edit/{id}", name="events_edit")
     */
    public function editAction($id, Request $request)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        $event->setTitle($event->getTitle());
        $event->setDetails($event->getDetails());
        $event->setDay($event->getDay());
        $event->setAddress($event->getAddress());
        $event->setCategory($event->getCategory());

        $form = $this->createFormBuilder($event)
        ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('category', EntityType::class, array('class' => 'AppBundle:Category','choice_label' => 'name', 'attr' => array('class' => 'form-control')))
        ->add('details', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('day', DateTimeType::class, array('attr' => array('class' => 'form-control-day', 'style' => 'margin-bottom:20px;')))
        ->add('address', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
        ->add('save', SubmitType::class, array('label' => 'Edit Event', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form['title']->getData();
            $category = $form['category']->getData();
            $details = $form['details']->getData();
            $day = $form['day']->getData();
            $address = $form['address']->getData();

            $em = $this->getDoctrine()->getManager();

            $event = $em->getRepository('AppBundle:Event')->find($id);
            $event->setTitle($title);
            $event->setDetails($details);
            $event->setDay($day);
            $event->setAddress($address);
            $event->setCategory($category);

            $em->flush();

            $this->addFlash('notice', 'Event Edited');

            return $this->redirectToRoute('events_edit');

        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/events/delete/{id}", name="events_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        $em->remove($event);
        $em->flush();
        $this->addFlash('notice', 'Event Deleted');

        return $this->redirectToRoute('events_list');
    }
}
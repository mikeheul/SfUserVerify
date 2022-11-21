<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * Lister les événements
     * @Route("/event", name="app_event")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // All events order by date ASC
        $events = $doctrine->getRepository(Event::class)->findBy([], ["startAt" => "ASC"]);

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * Inscrire l'utilisateur connecté à un événement 
     * @Route("/event/subscribe/{id}", name="user_subscribe")
     */
    public function subscribe(ManagerRegistry $doctrine, Event $event): Response
    {
        if($this->getUser()) {
            $event->addUser($this->getUser());
            $manager = $doctrine->getManager();
            $manager->persist($event);
            $manager->flush();

            return $this->redirectToRoute('app_event');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * Désinscrire l'utilisateur connecté d'un événement
     * @Route("/event/unsubscribe/{id}", name="user_unsubscribe")
     */
    public function unsubscribe(ManagerRegistry $doctrine, Event $event): Response
    {
        if($this->getUser()) {
            $event->removeUser($this->getUser());
            $manager = $doctrine->getManager();
            $manager->persist($event);
            $manager->flush();
    
            return $this->redirectToRoute('app_event');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * Ajouter un événement
     * @Route("/event/add", name="event_add")
     */
    public function add(Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager) 
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/add.html.twig', [
            'formAddEvent' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/delete/{id}", name="event_delete")
     */
    public function delete(Event $event, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($event);
        $entityManager->flush();
        return $this->redirectToRoute('app_event');
    }

}

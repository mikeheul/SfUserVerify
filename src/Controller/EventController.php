<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="app_event")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/event/subscribe/{id}", name="user_subscribe")
     */
    public function subscribe(ManagerRegistry $doctrine, Event $event): Response
    {
        $event->addUser($this->getUser());
        $manager = $doctrine->getManager();
        $manager->persist($event);
        $manager->flush();

        return $this->redirectToRoute('app_event');
    }

    /**
     * @Route("/event/unsubscribe/{id}", name="user_unsubscribe")
     */
    public function unsubscribe(ManagerRegistry $doctrine, Event $event): Response
    {
        $event->removeUser($this->getUser());
        $manager = $doctrine->getManager();
        $manager->persist($event);
        $manager->flush();

        return $this->redirectToRoute('app_event');
    }
}

<?php

namespace App\Controller;

use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('send-event', name: 'send_event')]
    public function sendEvent(EntityManagerInterface $em, HubInterface $hub): Response
    {
        $command = new Command();
        $em->persist($command);
        $em->flush();

        $update = new Update(
            '/api/command',
            json_encode(['id' => $command->getId()], JSON_THROW_ON_ERROR),
            false,
            'myCustomEventId',
        );
        $hub->publish($update);
        return $this->json(true);
    }
}

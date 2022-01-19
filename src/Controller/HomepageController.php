<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Room;

use Doctrine\Persistence\ManagerRegistry;

//https://symfony.com/doc/current/doctrine.html#fetching-objects-from-the-database

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $rooms = $doctrine->getRepository(Room::class)->findAll();

        return $this->render('homepage/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Bookings;
use App\Entity\Room;
use App\Entity\User;


class BookingController extends AbstractController
{
    #[Route('/booking', name: 'booking')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
//        $users = $doctrine->getRepository(User::class);
        $roomId = $request->query->get('id');

        return $this->render('booking/index.html.twig', [
            'roomId' => $roomId,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Bookings;
use App\Entity\Room;
use App\Entity\User;
use App\Form\BookingType;

class BookingController extends AbstractController
{
    //requestStack saves session variables
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/booking', name: 'booking')]
    public function index(ManagerRegistry $doctrine, Request $request, SessionInterface $session): Response
    {
        $users = $doctrine->getRepository(User::class);
        $roomId = $request->query->get('id');
        $form = $this->createForm(BookingType::class);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $booking = $form->getData();
            $session->set('startTime', $booking->getStartDate());
            $session->set('endTime', $booking->getEndDate());
            $session->set('roomID', $roomId);
            //TODO: userID?
            $session->set('userId', 1);

            // ... perform some action, such as saving the task to the database
            //TODO: view for this route.
            return $this->redirectToRoute('success');
        }

        return $this->renderForm('booking/index.html.twig', [
            'roomId' => $roomId,
            'form' => $form,
        ]);
    }

    #[Route('/success', name: 'success')]
    public function success(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = $this->requestStack->getSession();

//        $user = $doctrine->getManager()->getRepository(User::class)->find($request['userId']);
//        $room = $doctrine->getManager()->getRepository(Room::class)->find($request['roomId']);

    }
}

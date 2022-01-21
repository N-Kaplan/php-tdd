<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        $roomId = $request->query->get('id');
        $room = $doctrine->getRepository(Room::class)->find($roomId);
        $session = $this->requestStack->getSession();

        //error
        $errorMessage = $session->get('errorMessage') ?: "";

        $form = $this->createForm(BookingType::class)
            ->add('roomId', EntityType::class, ['data' => $room, 'class' => Room::class])
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choices' => $users,
            ])
            ->add('submit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values but, the original `$booking` variable has also been updated
            $booking = $form->getData();
            $session->set('startTime', $booking->getStartDate());
            $session->set('endTime', $booking->getEndDate());
            $session->set('roomId', $booking->getRoomId());
            $session->set('userId', $booking->getUserId());

            return $this->redirectToRoute('success');
        }

        return $this->renderForm('booking/index.html.twig', [
            'form' => $form,
            'errorMessage' => $errorMessage,
        ]);
    }

    #[Route('/success', name: 'success')]
    public function success(ManagerRegistry $doctrine): Response
    {
        $session = $this->requestStack->getSession();

        //error
        $errorMessage = "";
        $session->set('errorMessage', $errorMessage);

        //properties
        $userId = $session->get('userId')->getId();
        $user = $doctrine->getManager()->find(User::class, $userId);
        $roomId = $session->get('roomId')->getId();
        $room = $doctrine->getManager()->find(Room::class, $roomId);
        $startTime = $session->get('startTime');
        $endTime = $session->get('endTime');

        //implementing restrictions set by tests
        //premium room can only be booked by premium user
        $canBook = $room->canBook($user);

        //a room can only be booked when free
        $reservations = $room->getReservations($doctrine);
        $isFree = $room->isFree($startTime, $endTime, $reservations);

        //rooms can be booked up to 4 hours
        $canBookTime = $room->canBookTime($startTime, $endTime);

        //check if a user has enough credit
        $secondsBooked = ($endTime)->getTimestamp() - ($startTime)->getTimestamp();
        $hoursBooked = $secondsBooked % 3600 ? intdiv($secondsBooked, 3600) + 1 : intdiv($secondsBooked, 3600);
        $canPay = $user->canPay($hoursBooked);
        //user pays for booking
        $pricePerHour = 2;
        $user->pay($hoursBooked * $pricePerHour);

        // Book a room
        if ($canBook && $canBookTime && $isFree && $canPay) {
            $booking = new Bookings();
            $booking->setUserId($user);
            $booking->setRoomId($room);
            $booking->setStartDate($startTime);
            $booking->setEndDate($endTime);

//          see https://symfony.com/doc/current/doctrine.html#relationships-and-associations
            $entityManager = $doctrine->getManager();
            // tell Doctrine you want to (eventually) save the booking, user, room (no queries yet)
            $entityManager->persist($booking);
            $entityManager->persist($user);
            $entityManager->persist($room);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            //display booking
            return $this->render('booking/success.html.twig', [
                'username' => $session->get('userId')->getUsername(),
                'startTime' => $session->get('startTime'),
                'endTime' => $session->get('endTime'),
                'roomName' => $session->get('roomId')->getName(),
            ]);
        } elseif (!$canBook) {
            $errorMessage = "You do not have access to premium rooms, please select a regular room.";
        } elseif (!$canPay) {
            $errorMessage = "You do not have enough credit for this booking.";
        } elseif (!$canBookTime) {
            $errorMessage = "A room can be booked for up to 4 hours. Please select your booking time accordingly";
        } elseif (!$isFree) {
            $errorMessage = "The room you selected is booked in this timeframe, please select another room or a different time.";
        }

        //if booking is invalid
        $session->set('errorMessage', $errorMessage);

        return $this->redirectToRoute("booking", [
            'id' => $session->get('roomId')->getId()]);
    }
}

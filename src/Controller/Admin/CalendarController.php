<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Enum\CourseStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/calendar')]
class CalendarController extends AbstractController
{
    #[Route('', name: 'admin_calendar', methods: ['GET'])]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('calendar/index.html.twig');
    }

    // FullCalendar appelle cette route avec ?start=...&end=...
    #[Route('/events', name: 'admin_calendar_events', methods: ['GET'])]
    public function events(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $startParam = $request->query->get('start'); // ISO 8601
        $endParam   = $request->query->get('end');

        // sécurité : si non fournis, on prend le mois courant
        $start = $startParam ? new \DateTimeImmutable($startParam) : (new \DateTimeImmutable('first day of this month'))->setTime(0,0);
        $end   = $endParam   ? new \DateTimeImmutable($endParam)   : (new \DateTimeImmutable('last day of this month'))->setTime(23,59,59);

        // Récupérer les cours dans la plage (tu peux raffiner avec un repo)
        $qb = $em->getRepository(Course::class)->createQueryBuilder('c')
            ->where('c.dateCourse BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('c.dateCourse', 'ASC');

        $courses = $qb->getQuery()->getResult();

       // ...
$events = [];
foreach ($courses as $course) {
    /** @var Course $course */
    $date  = $course->getDateCourse();
    $startT = $course->getStartTime();
    $endT   = $course->getEndTime();

    $startDt = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $startT->format('H:i:s'));
    $endDt   = new \DateTimeImmutable($date->format('Y-m-d') . ' ' . $endT->format('H:i:s'));

    $mentorName = (string)($course->getMentor() ? $course->getMentor()->__toString() : '—');
    $studentNames = implode(', ', array_map(
        fn($s) => method_exists($s, 'getFullName') && $s->getFullName() ? $s->getFullName()
                    : trim(($s->getFirstName() ?? '').' '.($s->getLastName() ?? '')),
        $course->getStudents()->toArray()
    ));
    $subject = $course->getSubject()->value ?? 'Cours';
    $price = number_format($course->getPrice() /100 , 2, ',', ' ');

    $bg = match ($course->getStatus()) {
        \App\Enum\CourseStatus::PROGRAMME => '#f59e0b', // orange
        \App\Enum\CourseStatus::FINI      => '#10b981', // vert
        \App\Enum\CourseStatus::ANNULE    => '#ef4444', // rouge
        default                           => '#3b82f6', // bleu
    };

    $editUrl = $this->generateUrl('admin', [
        'crudAction' => 'edit',
        'crudControllerFqcn' => \App\Controller\Admin\CourseCrudController::class,
        'entityId' => $course->getId(),
    ]);

    $events[] = [
        'id'    => (string)$course->getId(),
        'title' => $subject, // court: on met le détail dans eventContent
        'start' => $startDt->modify('-2 hours')->format(\DateTimeInterface::ATOM),
        'end'   => $endDt->modify('-2 hours')->format(\DateTimeInterface::ATOM),
        'url'   => $editUrl,
        'allDay' => false,

        // style
        'backgroundColor' => $bg,
        'borderColor'     => $bg,
        'textColor'       => '#ffffff',

        // données pour l’affichage custom
        'extendedProps' => [
            'mentor'   => $mentorName ?: '—',
            'students' => $studentNames ?: '—',
            'subject'  => $subject,
            'price'    => $price,
            'status'   => $course->getStatus()->value ?? '',
            'startHm'  => $startDt->format('H:i'),
            'endHm'    => $endDt->format('H:i'),
        ],
    ];
}
// ...


        return $this->json($events);
    }
}

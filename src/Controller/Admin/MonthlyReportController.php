<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyReportController extends AbstractController
{
    #[Route('/admin/monthly-report', name: 'admin_monthly_report')]
    public function index(EntityManagerInterface $em): Response
{
    $start = new \DateTime('first day of this month 00:00:00');
    $end = new \DateTime('last day of this month 23:59:59');

    $courses = $em->getRepository(Course::class)->createQueryBuilder('c')
        ->where('c.dateCourse BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getResult();

    // Calcul du nombre de mentors uniques et préparation top mentors
    $mentorsData = []; // [mentorId => ['mentor' => Mentor, 'totalCourses' => int, 'totalRevenue' => float]]
    $studentsData = []; // [studentId => ['student' => Student, 'totalCourses' => int, 'totalSpent' => float]]
    
    foreach ($courses as $course) {
        $mentor = $course->getMentor();
        if ($mentor) {
            $id = $mentor->getId();
            if (!isset($mentorsData[$id])) {
                $mentorsData[$id] = [
                    'mentor' => $mentor,
                    'totalCourses' => 0,
                    'totalRevenue' => 0.0,
                ];
            }
            $mentorsData[$id]['totalCourses']++;
            $mentorsData[$id]['totalRevenue'] += $course->getPrice();
        }

        $students = $course->getStudents();
        foreach ($students as $student)
        {
        if ($student) {
            $id = $student->getId();
            if (!isset($studentsData[$id])) {
                $studentsData[$id] = [
                    'student' => $student,
                    'totalCourses' => 0,
                    'totalSpent' => 0.0,
                ];
            }
            $studentsData[$id]['totalCourses']++;
            $studentsData[$id]['totalSpent'] += $course->getPrice();
        }
    }
    }
    $totalMentors = count($mentorsData);
    $totalStudents = count($studentsData);

    // Calcul des cours par matière (supposons que getSubject() existe)
    $subjects = [];
    foreach ($courses as $course) {
        $subject = $course->getSubject();
        $subjectKey = $subject ? $subject->value : 'Inconnu';
        if (!isset($subjects[$subjectKey])) {
            $subjects[$subjectKey] = 0;
        }
        $subjects[$subjectKey]++;
    }

    // Top 5 mentors par nombre de cours
    usort($mentorsData, function($a, $b) {
        return $b['totalCourses'] <=> $a['totalCourses'];
    });
    $topMentors = array_slice($mentorsData, 0, 5);

    // Top 5 students par nombre de cours
    usort($studentsData, function($a, $b) {
        return $b['totalCourses'] <=> $a['totalCourses'];
    });
    $topStudents = array_slice($studentsData, 0, 5);

    // Préparer un tableau plus simple pour Twig
    $topMentorsForTwig = [];
    foreach ($topMentors as $data) {
        $topMentorsForTwig[] = [
            'name' => $data['mentor']->__toString(),
            'totalCourses' => $data['totalCourses'],
            'totalRevenue' => $data['totalRevenue'],
        ];
    }

    $topStudentsForTwig = [];
  
    foreach ($topStudents as $data) {
        $topStudentsForTwig[] = [
            'name' => $data['student']->__toString(),
            'totalCourses' => $data['totalCourses'],
            'totalSpent' => $data['totalSpent'],
        ];
    }

    $summaries = [
        'totalCourses' => count($courses),
        'revenueTotal' => array_sum(array_map(fn($c) => $c->getPrice(), $courses)),
        'totalMentors' => $totalMentors,
        'totalStudents' => $totalStudents,
        'subjects' => $subjects,
        'topMentors' => $topMentorsForTwig,
        'topStudents' => $topStudentsForTwig,
    ];

    return $this->render('monthly-report/index.html.twig', [
        'courses' => $courses,
        'summaries' => $summaries,
        'monthName' => (new \IntlDateFormatter('fr_FR', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE))->format($start),
        'year' => (int) $start->format('Y'),
    ]);
}

}

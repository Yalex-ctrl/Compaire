<?php

namespace App\Controller\Admin;

use App\Entity\Parents;
use App\Repository\ParentsRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class ParentInvoiceController extends AbstractController
{
    #[Route('/admin/parent-invoices', name: 'admin_parent_invoices')]
public function index(Request $request, ParentsRepository $parentsRepository, CourseRepository $courseRepository): Response
{
    $monthString = $request->query->get('month') ?? date('Y-m');
    $month = new \DateTime($monthString . '-01');
    $start = (clone $month)->setTime(0, 0, 0);
    $end = (clone $month)->modify('last day of this month')->setTime(23, 59, 59);

    // Récupérer tous les cours avec étudiants liés dans la période
    $courses = $courseRepository->createQueryBuilder('c')
        ->leftJoin('c.students', 's')->addSelect('s')
        ->where('c.startTime BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery()
        ->getResult();

    $parentMap = [];

    foreach ($courses as $course) {
        foreach ($course->getStudents() as $student) {
            $parent = $student->getParents(); // ou ->getParents() si plusieurs
            if ($parent) {
                $parentMap[$parent->getId()] = $parent; // empêche les doublons
            }
        }
    }

    $filteredParents = array_values($parentMap);

    return $this->render('invoices/parent-index.html.twig', [
        'parents' => $filteredParents,
        'month' => $monthString,
    ]);
}



    #[Route('/admin/parent-invoices/{id}', name: 'admin_parent_invoice_detail')]
    public function show(Parents $parents, Request $request, CourseRepository $courseRepository): Response
    {
        $monthString = $request->query->get('month');
        if ($monthString && \DateTime::createFromFormat('Y-m', $monthString) !== false) {
            $month = new \DateTime($monthString . '-01');
        } else {
            $month = new \DateTime('first day of last month');
        }

        $start = (clone $month)->setTime(0, 0, 0);
        $end = (clone $month)->modify('last day of this month')->setTime(23, 59, 59);

        $students = $parents->getStudents();
        $courses = [];

        foreach ($students as $student) {
            $studentCourses = $courseRepository->createQueryBuilder('c')
                ->leftJoin('c.students', 's')
                ->where('s.id = :studentId')
                ->andWhere('c.startTime BETWEEN :start AND :end')
                ->setParameter('studentId', $student->getId())
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();
          //  dump($student);
        //    dd($studentCourses);
            foreach ($studentCourses as $course) {
                $courses[] = [
                    'student' => $student,
                    'course' => $course
                ];
            }
        }

        usort($courses, fn($a, $b) => $a['course']->getStartTime() <=> $b['course']->getStartTime());

        $total = array_reduce($courses, fn($carry, $item) => $carry + $item['course']->getPrice(), 0);

        $html = $this->renderView('invoices/parent-details.html.twig', [
            'parent' => $parents,
            'courses' => $courses,
            'total' => $total,
            'month' => $start->format('Y-m'),
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $filename = 'facture-parent-' . $parents->getId() . '-' . $start->format('Y-m') . '.pdf';

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Mentor;
use App\Repository\MentorRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;

class InvoiceController extends AbstractController
{
    #[Route('/admin/invoices', name: 'admin_mentor_invoices')]
    public function index(Request $request, MentorRepository $mentorRepository): Response
    {
        // Récupère le paramètre "month" en GET (format YYYY-MM) ou utilise le mois actuel
        $month = $request->query->get('month') ?? date('Y-m');

        // Vérification et conversion en DateTime
        if (\DateTime::createFromFormat('Y-m', $month) !== false) {
            $start = new \DateTime($month . '-01 00:00:00');
            $end = (clone $start)->modify('last day of this month')->setTime(23, 59, 59);
        } else {
            $start = new \DateTime('first day of this month');
            $end = (clone $start)->modify('last day of this month')->setTime(23, 59, 59);
            $month = $start->format('Y-m');
        }

        // Récupère uniquement les mentors ayant donné au moins un cours dans l'intervalle
        $mentors = $mentorRepository->findMentorsWithCoursesInMonth($start, $end);

        return $this->render('invoices/index.html.twig', [
            'mentors' => $mentors,
            'month' => $month,
        ]);
    }

    #[Route('/admin/invoices/{id}', name: 'admin_mentor_invoice_detail')]
    public function show(Mentor $mentor, Request $request, CourseRepository $courseRepository): Response
    {
        // Récupère le mois (format YYYY-MM)
        $monthString = $request->query->get('month');
        if ($monthString && \DateTime::createFromFormat('Y-m', $monthString) !== false) {
            $month = new \DateTime($monthString . '-01');
        } else {
            $month = new \DateTime('first day of last month');
        }

        $start = (clone $month)->setTime(0, 0, 0);
        $end = (clone $month)->modify('last day of this month')->setTime(23, 59, 59);

        // Récupère les cours du mentor pour le mois sélectionné
        $courses = $courseRepository->createQueryBuilder('c')
            ->where('c.mentor = :mentor')
            ->andWhere('c.dateCourse BETWEEN :start AND :end')
            ->setParameter('mentor', $mentor)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        $total = array_reduce($courses, fn($carry, $course) => $carry + $course->getPrice(), 0);
        $mentorShare = $total * 0.8;

        // Génère la vue HTML pour le PDF
        $html = $this->renderView('invoices/details.html.twig', [
            'mentor' => $mentor,
            'courses' => $courses,
            'total' => $total,
            'mentorShare' => $mentorShare,
            'month' => $start->format('Y-m'),
        ]);

        // Instancie Dompdf et génère le PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'facture-' . $mentor->getId() . '-' . $start->format('Y-m') . '.pdf';
        $pdfContent = $dompdf->output();

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

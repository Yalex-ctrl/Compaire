<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\Mentor;
use App\Entity\Parents;
use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // redirection directe vers une entité CRUD, ici Course
        $url = $this->container->get(AdminUrlGenerator::class)
            ->setController(\App\Controller\Admin\CourseCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
{
    return Dashboard::new()
        ->setTitle('<img src="/images/logo.png" alt="Compaire" style="height: 40px;">');
}


    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Calendrier', 'fa fa-calendar', 'admin_calendar');
        yield MenuItem::linkToCrud('Cours', 'fas fa-book', Course::class);
        yield MenuItem::linkToCrud('Mentors', 'fas fa-user-tie', Mentor::class);
        yield MenuItem::linkToCrud('Parents', 'fas fa-users', Parents::class);
        yield MenuItem::linkToCrud('Étudiants', 'fas fa-user-graduate', Student::class);
        yield MenuItem::linkToRoute('Factures Mentors', 'fa fa-file-invoice', 'admin_mentor_invoices');
        yield MenuItem::linkToRoute('Factures Parents', 'fa fa-file-invoice', 'admin_parent_invoices');
        yield MenuItem::linkToRoute('Statistiques du mois', 'fa fa-chart-bar', 'admin_monthly_report');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Enum\Subject;
use App\Enum\CourseStatus;
use App\Enum\PaymentStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setEntityLabelInSingular('Cours')
            ->setEntityLabelInPlural('Cours');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
                $action->setIcon('fa fa-pencil-alt')->setLabel(false)->addCssClass('text-warning')
            )
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $action) =>
                $action->setIcon('fa fa-eye')->setLabel(false)->addCssClass('text-info')
            );
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('mentor', 'Mentor'))
            ->add(EntityFilter::new('students', 'Étudiants'))
            ->add(DateTimeFilter::new('startTime', 'Date'));
    }

    public function configureFields(string $pageName): iterable
    {
        $dateRangeField = Field::new('startTime', 'Date et heure')
            ->onlyOnIndex()
            ->formatValue(function ($value, $entity) {
                if (!$entity) return '';
                $start = $entity->getStartTime();
                $end = $entity->getEndTime();
                if (!$start || !$end) return '';
                $day = $start->format('j');
                $monthFR = match (strtolower($start->format('F'))) {
                    'january' => 'janvier',
                    'february' => 'février',
                    'march' => 'mars',
                    'april' => 'avril',
                    'may' => 'mai',
                    'june' => 'juin',
                    'july' => 'juillet',
                    'august' => 'août',
                    'september' => 'septembre',
                    'october' => 'octobre',
                    'november' => 'novembre',
                    'december' => 'décembre',
                    default => $start->format('F'),
                };
                return sprintf('le %d %s de %dh à %dh', $day, $monthFR, (int)$start->format('H'), (int)$end->format('H'));
            });

        return [
            AssociationField::new('mentor')->setLabel('Mentor'),

            AssociationField::new('students')
                ->setLabel('Étudiants')
                ->setFormTypeOption('by_reference', false)
                ->setTemplatePath('students/students-name-field.html.twig'),

            $dateRangeField,

            MoneyField::new('price')
                ->setLabel('Prix')
                ->setCurrency('EUR')
                ->setTemplatePath('course/price-colored.html.twig'),

            TextField::new('subjectLabel', 'Matière')->onlyOnIndex(),

            ChoiceField::new('subject')
                ->setLabel('Matière')
                ->setChoices(array_combine(
                    array_map(fn($s) => ucfirst($s->value), Subject::cases()),
                    Subject::cases()
                ))
                ->onlyOnForms(),

            ChoiceField::new('status')
                ->setLabel('Statut')
                ->setChoices(array_combine(
                    array_map(fn($s) => ucfirst($s->value), CourseStatus::cases()),
                    CourseStatus::cases()
                ))
                ->renderAsBadges([
                    'programmé' => 'warning',
                    'fini' => 'success',
                    'annulé' => 'danger',
                ]),

            ChoiceField::new('paymentStatus')
                ->setLabel('Statut de paiement')
                ->setChoices(array_combine(
                    array_map(fn($p) => ucfirst($p->value), PaymentStatus::cases()),
                    PaymentStatus::cases()
                ))
                ->renderAsBadges([
                    'payé' => 'success',
                    'non payé' => 'danger',
                    'en cours' => 'warning',
                ]),

            TextareaField::new('mentorNotes')->setLabel('Notes du mentor'),

            TextareaField::new('notes')->setLabel('Notes du cours')->onlyOnIndex(false),

            DateTimeField::new('startTime')->setLabel('Heure début')->onlyOnForms(),
            DateTimeField::new('endTime')->setLabel('Heure fin')->onlyOnForms(),

            DateTimeField::new('createdAt')->setLabel('Créé le')->onlyOnForms()->onlyWhenUpdating(),
            DateTimeField::new('updatedAt')->setLabel('Modifié le')->onlyOnForms()->onlyWhenUpdating(),
        ];
    }
}

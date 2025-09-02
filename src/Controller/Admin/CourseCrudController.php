<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Enum\Subject;
use App\Enum\CourseStatus;
use App\Enum\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('mentor', 'Mentor'))
            ->add(EntityFilter::new('students', 'Étudiants'))
            ->add(DateTimeFilter::new('dateCourse', 'Date'));
    }

    public function configureFields(string $pageName): iterable
    {
        $dateRangeField = Field::new('dateCourse', 'Date et heure')
            ->onlyOnIndex()
            ->formatValue(function ($value, $entity) {
                if (!$entity) return '';
                $date  = $entity->getDateCourse();
                $start = $entity->getStartTime();
                $end   = $entity->getEndTime();
                if (!$date || !$start || !$end) return '';
                return sprintf('le %s de %s à %s',
                    $date->format('d-m-y'),
                    $start->format('H:i'),
                    $end->format('H:i')
                );
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

            DateField::new('dateCourse', 'Date')
                ->onlyOnForms()
                ->setFormTypeOption('widget', 'single_text')
                ->setFormTypeOption('html5', false)
                ->setFormTypeOption('attr', ['placeholder' => 'JJ-MM-AA']),

            TimeField::new('startTime', 'Heure début')
                ->onlyOnForms()
                ->setFormTypeOption('widget', 'single_text')
                ->setFormTypeOption('html5', false)
                ->setFormTypeOption('attr', ['placeholder' => 'HH:mm / 9h30 / 930']),

            TimeField::new('endTime', 'Heure fin')
                ->onlyOnForms()
                ->setFormTypeOption('widget', 'single_text')
                ->setFormTypeOption('html5', false)
                ->setFormTypeOption('attr', ['placeholder' => 'HH:mm / 14 / 14:00']),

            DateTimeField::new('createdAt')->setLabel('Créé le')->onlyOnForms()->onlyWhenUpdating(),
            DateTimeField::new('updatedAt')->setLabel('Modifié le')->onlyOnForms()->onlyWhenUpdating(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new('duplicate', '', 'fa fa-copy')
            ->linkToCrudAction('duplicate')
            ->addCssClass('text-success')
            ->displayIf(static fn($entity) => $entity instanceof Course); // action de ligne uniquement

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
                $action->setIcon('fa fa-pencil-alt')->setLabel(false)->addCssClass('text-warning')
            )
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $action) =>
                $action->setIcon('fa fa-eye')->setLabel(false)->addCssClass('text-info')
            )
            ->add(Crud::PAGE_INDEX, $duplicate);
    }

    /**
     * Duplique un cours (date +7 jours) puis revient à la liste.
     * ⚠️ Ne JAMAIS appeler $context->getEntity() ici (peut être null) :
     * on récupère l'ID via la query 'entityId' et on charge avec Doctrine.
     */
    public function duplicate(
        AdminContext $context,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        // Récupère l'ID depuis la requête (action de ligne EasyAdmin)
        $request = $context->getRequest();
        $id = $request->query->get('entityId') ?? $request->attributes->get('entityId');
        if (!$id) {
            $this->addFlash('danger', 'ID manquant pour la duplication.');
            $url = $adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl();
            return $this->redirect($url);
        }

        /** @var Course|null $source */
        $source = $em->getRepository(Course::class)->find($id);
        if (!$source) {
            $this->addFlash('danger', 'Cours introuvable.');
            $url = $adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl();
            return $this->redirect($url);
        }

        // Création de la copie (+7 jours sur la date)
        $copy = new Course();
        $copy->setMentor($source->getMentor());

        if ($source->getDateCourse()) {
            $copy->setDateCourse((clone $source->getDateCourse())->modify('+7 days'));
        }
        if ($source->getStartTime()) {
            $copy->setStartTime(clone $source->getStartTime());
        }
        if ($source->getEndTime()) {
            $copy->setEndTime(clone $source->getEndTime());
        }

        $copy->setSubject($source->getSubject());
        $copy->setPrice($source->getPrice());
        $copy->setStatus($source->getStatus());
        $copy->setPaymentStatus($source->getPaymentStatus());
        $copy->setNotes($source->getNotes());
        $copy->setMentorNotes($source->getMentorNotes());
        $copy->setCreatedAt(new \DateTimeImmutable());
        $copy->setUpdatedAt(new \DateTimeImmutable());

        foreach ($source->getStudents() as $student) {
            $copy->addStudent($student);
        }

        $em->persist($copy);
        $em->flush();

        $this->addFlash('success', 'Cours dupliqué (+7 jours).');

        $url = $adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl();
        return $this->redirect($url);
    }
}

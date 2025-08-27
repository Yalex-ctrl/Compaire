<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Enum\ClassLevel;
use App\Enum\StudentStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StudentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined() // Affiche les icônes sur la même ligne
            ->setEntityLabelInSingular('Élève')
            ->setEntityLabelInPlural('Élèves')
            ->setDefaultSort(['lastName' => 'ASC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->remove(Crud::PAGE_INDEX, Action::DELETE) // Supprime le bouton "Supprimer" de l'index

            // Icône "Modifier" (jaune)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
                $action->setIcon('fa fa-pencil-alt')->setLabel(false)->addCssClass('text-warning')
            )

            // Icône "Détail" (bleu)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $action) =>
                $action->setIcon('fa fa-eye')->setLabel(false)->addCssClass('text-info')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('firstName', 'Prénom'),
            TextField::new('lastName', 'Nom'),

            ChoiceField::new('classLevel', 'Niveau scolaire')
                ->setChoices(array_combine(
                    array_map(fn($c) => $c->value, ClassLevel::cases()),
                    ClassLevel::cases()
                )),

            AssociationField::new('mentor', 'Mentor'),
            AssociationField::new('parents', 'Parents'),

            TextField::new('address', 'Adresse'),

            ArrayField::new('subjects', 'Matières'),

            IntegerField::new('weeklyHours', 'Heures par semaine'),

            TextField::new('usualSchedule', 'Emploi du temps habituel')
                ->formatValue(fn ($value, $entity) => strip_tags($value)),

            ChoiceField::new('status', 'Statut')
                ->setChoices(array_combine(
                    array_map(fn($s) => strtoupper($s->value), StudentStatus::cases()),
                    StudentStatus::cases()
                )),

            ChoiceField::new('convCompt', 'Conv Compt')
                ->setChoices(array_combine(
                    array_map(fn($s) => strtoupper($s->value), StudentStatus::cases()),
                    StudentStatus::cases()
                )),
        ];

        if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_DETAIL])) {
            $fields[] = TextField::new('notes', 'Notes')
                ->formatValue(fn ($value, $entity) => strip_tags($value));
        }

        return $fields;
    }
}

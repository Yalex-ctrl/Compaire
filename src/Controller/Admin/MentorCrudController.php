<?php

namespace App\Controller\Admin;

use App\Entity\Mentor;
use App\Enum\AutoStatusEnum;
use App\Enum\AccountReportEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class MentorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mentor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined() // Affiche les actions (icônes) sur la même ligne
            ->setEntityLabelInSingular('Mentor')
            ->setEntityLabelInPlural('Mentors');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE) // Supprime le bouton de suppression

            // Modifier : icône ✏️ jaune
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
                $action->setIcon('fa fa-pencil-alt')->setLabel(false)->addCssClass('text-warning')
            )

            // Détail : icône 👁️ bleu
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $action) =>
                $action->setIcon('fa fa-eye')->setLabel(false)->addCssClass('text-info')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('firstName')->setLabel('Prénom');
        yield TextField::new('lastName')->setLabel('Nom');

        yield AssociationField::new('students')
            ->setLabel('Étudiants')
            ->setFormTypeOptions(['by_reference' => false])
            ->setTemplatePath('students/students-name-field.html.twig');

        yield TextField::new('phone')->setLabel('Téléphone');

        yield ChoiceField::new('accountReport')
            ->setLabel('Rapport de compte')
            ->setChoices([
                'Oui' => AccountReportEnum::YES,
                'Non' => AccountReportEnum::NO,
            ])
            ->renderAsBadges([
                AccountReportEnum::YES->value => 'success',
                AccountReportEnum::NO->value => 'danger',
            ]);

        yield ChoiceField::new('autoStatus')
            ->setLabel('Statut auto')
            ->setChoices([
                'Oui' => AutoStatusEnum::YES,
                'Non' => AutoStatusEnum::NO,
                'En cours' => AutoStatusEnum::IN_PROGRESS,
            ])
            ->renderAsBadges([
                AutoStatusEnum::YES->value => 'success',
                AutoStatusEnum::NO->value => 'danger',
                AutoStatusEnum::IN_PROGRESS->value => 'warning',
            ]);

        yield TextareaField::new('notes')->setLabel('Notes')->hideOnIndex();
    }
}

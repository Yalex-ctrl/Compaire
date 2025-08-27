<?php

namespace App\Controller\Admin;

use App\Entity\Parents;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ParentsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Parents::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined() // Affiche les actions sur la même ligne
            ->setEntityLabelInSingular('Parent')
            ->setEntityLabelInPlural('Parents');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->remove(Crud::PAGE_INDEX, Action::DELETE) // Retire le bouton Supprimer

            // Modifier : icône jaune ✏️
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) =>
                $action->setIcon('fa fa-pencil-alt')->setLabel(false)->addCssClass('text-warning')
            )

            // Détail : icône bleue 👁️
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn(Action $action) =>
                $action->setIcon('fa fa-eye')->setLabel(false)->addCssClass('text-info')
            );
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('fullName', 'Nom complet'),

            TextField::new('address', 'Adresse'),

            TelephoneField::new('phone', 'Téléphone'),

            EmailField::new('email', 'Email'),

            TextField::new('referralSource', 'Comment ils nous ont connus')
                ->formatValue(fn ($value, $entity) => strip_tags($value)),

            AssociationField::new('students', 'Enfants')
                ->onlyOnIndex()
                ->setTemplatePath('students/parents-students-field.html.twig'),
        ];
    }
}

<?php

namespace App\Controller\Admin\CrudControllers;

use App\Entity\BillingSettings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BillingSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BillingSettings::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Billing Settings')
                    ->setEntityLabelInPlural('Billing Settings');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            AssociationField::new('business')
                ->setRequired(true),

            TextField::new('invoicePrefix'),
            NumberField::new('invoiceStartNumber'),
            NumberField::new('defaultTaxRate'),
            TextField::new('terms'),
            TextField::new('logoUrl'),
        ];
    }
}

<?php

namespace App\Controller\Admin\CrudControllers;

use App\Entity\InvoiceItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InvoiceItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InvoiceItem::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Invoice Item')
                    ->setEntityLabelInPlural('Invoice Items');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('description'),

            ChoiceField::new('type')
                ->setChoices([
                    'Product' => 'product',
                    'Service' => 'service',
                    'Labour' => 'labour',
                    'Fee' => 'fee',
                    'Discount' => 'discount',
                ]),

            NumberField::new('quantity')->setNumDecimals(2),

            NumberField::new('unitPrice')->setNumDecimals(2),

            NumberField::new('taxRate')->setHelp('GST percentage, e.g. 18'),

            TextField::new('hsnSac')->hideOnIndex(),

            NumberField::new('lineTotal')
                ->onlyOnDetail()
                ->setNumDecimals(2),
        ];
    }
}

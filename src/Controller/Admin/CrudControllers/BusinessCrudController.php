<?php

namespace App\Controller\Admin\CrudControllers;

use App\Entity\Business;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class BusinessCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $urlGenerator;

    public function __construct(AdminUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    public static function getEntityFqcn(): string
    {
        return Business::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewInvoices = Action::new('viewInvoices', 'Invoices')
            ->linkToCrudAction('invoicesForBusiness');

        $editBilling = Action::new('editBilling', 'Billing Settings')
            ->linkToCrudAction('editBillingForBusiness');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // ->add(Crud::PAGE_INDEX, $viewInvoices)
            ->add(Crud::PAGE_DETAIL, $editBilling)
            ->add(Crud::PAGE_DETAIL, $viewInvoices)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function invoicesForBusiness(AdminContext $context)
    {
        $business = $context->getEntity()->getInstance();

        $url = $this->urlGenerator
            ->setController(InvoiceCrudController::class)
            ->setAction('index')
            ->set('filters[business][value]', $business->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function editBillingForBusiness(AdminContext $context)
    {
        $business = $context->getEntity()->getInstance();
        $billing = $business->getBillingSettings();

        $url = $this->urlGenerator
            ->setController(BillingSettingsCrudController::class)
            ->setAction('edit')
            ->setEntityId($billing->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();

        $name = TextField::new('name');
        $gstEnabled = BooleanField::new('gstEnabled');
        $gstin = TextField::new('gstin');
        $address = TextareaField::new('address');

        // $owner = AssociationField::new('owner')->setFormTypeOption('disabled', true);
        $owner = AssociationField::new('owner')
            ->setRequired(true)
            ->setHelp('Select the owner of this business (required)');

        $billingSettings = AssociationField::new('billingSettings')->hideOnForm();

        $createdAt = DateTimeField::new('createdAt')->setDisabled();
        $updatedAt = DateTimeField::new('updatedAt')->setDisabled();
        
        $invoicesField = CollectionField::new('invoices')
        ->setTemplatePath('admin/fields/invoices_list.html.twig')
        ->onlyOnDetail();

        if ($pageName === Crud::PAGE_INDEX) {
            return [
                $id, $name, $gstEnabled, $gstin, $owner
            ];
        }

        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                $id, $name, $gstEnabled, $gstin, $address,
                $owner, $billingSettings, $invoicesField,
                $createdAt, $updatedAt,
            ];
        }

        return [
            $name,
            $owner, 
            $gstEnabled,
            $gstin,
            $address,
            
        ];
    }
}

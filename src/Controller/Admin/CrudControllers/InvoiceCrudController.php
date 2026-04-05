<?php

namespace App\Controller\Admin\CrudControllers;

use App\Entity\Invoice;
use App\Form\InvoiceItemType;
use Symfony\Component\Mailer\MailerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
// use Symfony\Component\Validator\Constraints\Email;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Routing\Generator\UrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Mime\Email;



class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        $downloadPdf = Action::new('downloadPdf', 'Download PDF')
            ->linkToUrl(fn (Invoice $invoice) =>
                $this->generateUrl('invoice_pdf', ['id' => $invoice->getId()])
            )
            ->setIcon('fa fa-file-pdf');

        $print = Action::new('printInvoice', 'Print')
            ->linkToUrl(fn (Invoice $invoice) =>
                $this->generateUrl('invoice_print', ['id' => $invoice->getId()])
            )
            ->setIcon('fa fa-print')
            ->addCssClass('btn btn-secondary')
            ->setHtmlAttributes(['target' => '_blank']);
        
        $mail = Action::new('mailInvoice', 'Send Email')
            ->linkToCrudAction('sendInvoiceEmail')
            ->setIcon('fa fa-envelope');


        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
            ->add(Crud::PAGE_DETAIL, $downloadPdf)
            ->add(Crud::PAGE_DETAIL, $print)
            ->add(Crud::PAGE_DETAIL, $mail);
    }

    public function sendInvoiceEmail(AdminContext $context, MailerInterface $mailer)
    {
        /** @var Invoice $invoice */
        $invoice = $context->getEntity()->getInstance();

        $email = (new Email())
            ->from('no-reply@billingapp.com')
            ->to($invoice->getCustomerPhone() . '@sms-email-gateway') // or customer email
            ->subject('Your Invoice ' . $invoice->getInvoiceNumber())
            ->html($this->renderView('email/invoice.html.twig', [
                'invoice' => $invoice
            ]));

        $mailer->send($email);

        $this->addFlash('success', 'Invoice sent to customer!');

        return $this->redirect($context->getReferrer());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Invoice')
            ->setEntityLabelInPlural('Invoices')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();

        $invoiceNumber = TextField::new('invoiceNumber')
            ->setDisabled()
            ->setHelp('Auto-generated from Billing Settings');

        $date = DateField::new('date');

        $customerName = TextField::new('customerName');
        $customerPhone = TextField::new('customerPhone');
        $customerAddress = TextField::new('customerAddress');

        $business = AssociationField::new('business');

        $items = CollectionField::new('items')
            ->setEntryType(InvoiceItemType::class)
            ->setFormTypeOptions([
                'entry_options' => ['label' => false],
                'by_reference' => false,  // VERY IMPORTANT
            ])
            ->allowAdd()
            ->allowDelete()
            ->renderExpanded();

        $sub = MoneyField::new('subTotal')->setCurrency('INR')->hideOnForm();
        $tax = MoneyField::new('taxTotal')->setCurrency('INR')->hideOnForm();
        $total = MoneyField::new('total')->setCurrency('INR')->hideOnForm();

        $status = TextField::new('status');

        /** 📌 DETAIL PAGE: professional invoice preview */
        if ($pageName === Crud::PAGE_DETAIL) {

            $invoiceView = Field::new('invoiceView')
                ->setTemplatePath('admin/invoice/view.html.twig')
                ->setLabel('GST Invoice');

            return [
                $id,
                $invoiceNumber,
                $business,
                $date,
                $customerName,
                $customerPhone,
                $customerAddress,

                // Professional GST Invoice Preview
                $invoiceView,

                $items,
                $sub,
                $tax,
                $total,
                $status,
            ];
        }

        /** INDEX PAGE */
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                $id,
                $invoiceNumber,
                $business,
                $customerName,
                $total,
                $status,
            ];
        }

        /** FORM PAGE */
        return [
            $business,
            $date,
            $customerName,
            $customerPhone,
            $customerAddress,
            $items,
            $status,
        ];
    }
}

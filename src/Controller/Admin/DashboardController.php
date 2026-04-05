<?php

namespace App\Controller\Admin;

use App\Controller\Admin\CrudControllers\BusinessCrudController;
use App\Controller\Admin\CrudControllers\BillingSettingsCrudController;
use App\Controller\Admin\CrudControllers\InvoiceCrudController;
use App\Controller\Admin\CrudControllers\InvoiceItemCrudController;
use App\Controller\Admin\CrudControllers\UserCrudController;
use App\Controller\Admin\CrudControllers\RefreshTokenCrudController;
use App\Entity\Business;
use App\Entity\BillingSettings;
use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\User;
use App\Entity\RefreshToken;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirect to Business CRUD by default (change as you want)
        $url = $this->adminUrlGenerator
            ->setController(BusinessCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureMenuItems(): iterable
    {
        // Top dashboard / overview links
        yield MenuItem::section('Management');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Business group
        yield MenuItem::section('Business & Billing');
        yield MenuItem::linkToCrud('Businesses', 'fa fa-building', Business::class)
            ->setController(BusinessCrudController::class);
        yield MenuItem::linkToCrud('Billing Settings', 'fa fa-cog', BillingSettings::class)
            ->setController(BillingSettingsCrudController::class);

        // Invoices group
        yield MenuItem::section('Billing');
        yield MenuItem::linkToCrud('Invoices', 'fa fa-file-invoice', Invoice::class)
            ->setController(InvoiceCrudController::class);
        yield MenuItem::linkToCrud('Invoice Items', 'fa fa-list', InvoiceItem::class)
            ->setController(InvoiceItemCrudController::class);

        // Users & tokens (admin only)
        yield MenuItem::section('Users & Auth');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class)
            ->setController(UserCrudController::class);
        yield MenuItem::linkToCrud('Refresh Tokens', 'fa fa-key', RefreshToken::class)
            ->setController(RefreshTokenCrudController::class);

        // Optional: link to API docs or frontend
        yield MenuItem::section('Utilities');
        yield MenuItem::linkToUrl('API Docs', 'fa fa-book', $this->generateUrl('api_doc')) // adjust route if needed
            ;
    }
}

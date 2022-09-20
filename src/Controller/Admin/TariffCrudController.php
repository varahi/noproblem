<?php

namespace App\Controller\Admin;

use App\Entity\Tariff;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TariffCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tariff::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

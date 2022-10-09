<?php

namespace App\Controller\Admin;

use App\Entity\Citizen;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CitizenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Citizen::class;
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

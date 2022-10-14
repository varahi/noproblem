<?php

namespace App\Controller\Admin;

use App\Entity\Accommodation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccommodationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Accommodation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Accommodation')
            ->setEntityLabelInPlural('Accommodation')
            ->setSearchFields(['task'])
            ->setDefaultSort(['name' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        yield TextField::new('name')->setColumns('col-md-10');
        yield AssociationField::new('job')->setColumns('col-md-10')->hideOnIndex();
    }
}

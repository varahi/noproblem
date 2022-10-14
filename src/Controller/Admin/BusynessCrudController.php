<?php

namespace App\Controller\Admin;

use App\Entity\Busyness;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BusynessCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Busyness::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Busynnes')
            ->setEntityLabelInPlural('Busynnes')
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

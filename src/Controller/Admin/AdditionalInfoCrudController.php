<?php

namespace App\Controller\Admin;

use App\Entity\AdditionalInfo;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdditionalInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdditionalInfo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Additional info')
            ->setEntityLabelInPlural('Additional info')
            ->setSearchFields(['task'])
            ->setDefaultSort(['name' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('isHidden');
        yield TextField::new('name')->setColumns('col-md-10');
        yield AssociationField::new('jobs')->setColumns('col-md-10')->hideOnIndex();
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

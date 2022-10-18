<?php

namespace App\Controller\Admin;

use App\Entity\City;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class CityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return City::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('City')
            ->setEntityLabelInPlural('City')
            ->setSearchFields(['name', 'description'])
            ->setDefaultSort(['name' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-12');
        yield BooleanField::new('isHidden');
        yield TextField::new('name')->setColumns('col-md-10');
        yield TextareaField::new('description')->setColumns('col-md-10');
        yield NumberField::new('latitude')->hideOnIndex()->setFormTypeOption('scale', 8)->setColumns('col-md-10');
        yield NumberField::new('longitude')->hideOnIndex()->setFormTypeOption('scale', 8)->setColumns('col-md-10');
        yield AssociationField::new('districts')->setColumns('col-md-6')->hideOnIndex();
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

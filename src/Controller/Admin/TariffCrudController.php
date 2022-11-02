<?php

namespace App\Controller\Admin;

use App\Entity\Tariff;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class TariffCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tariff::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Tariff')
            ->setEntityLabelInPlural('Tariff')
            ->setSearchFields(['task'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name')->setColumns('col-md-10');
        yield TextareaField::new('description')->setColumns('col-md-10')->hideOnIndex();
        yield TextareaField::new('customerDescription')->setColumns('col-md-10')->hideOnIndex();
        yield TextareaField::new('price')->setColumns('col-md-10');
        yield TextareaField::new('priceComment')->setColumns('col-md-10')->hideOnIndex();
        yield TextareaField::new('oldPrice')->setColumns('col-md-10');
        yield IntegerField::new('amount')->setColumns('col-md-10')->hideOnIndex();
        yield AssociationField::new('orders')->setColumns('col-md-10');
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

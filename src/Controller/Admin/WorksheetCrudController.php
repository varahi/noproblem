<?php

namespace App\Controller\Admin;

use App\Entity\Worksheet;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class WorksheetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Worksheet::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Worksheet')
            ->setEntityLabelInPlural('Worksheet')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ->add(EntityFilter::new('client'))
            ->add(EntityFilter::new('owner'))
            ->add(EntityFilter::new('city'))
            ->add(EntityFilter::new('district'))
            ->add(EntityFilter::new('experience'))
            ->add(EntityFilter::new('education'))
            ->add(EntityFilter::new('citizen'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Main info')->setIcon('fa fa-info')->setCssClass('col-sm-8');
        //yield FormField::addRow();

        yield IntegerField::new('id')->setFormTypeOption('disabled', 'disabled')->hideWhenCreating();
        yield DateTimeField::new('created')->setFormTypeOption('disabled', 'disabled');
        yield BooleanField::new('hidden');
        yield TextField::new('name')->setColumns('col-md-8');
        yield TextareaField::new('description')->setColumns('col-md-8')->hideOnIndex();
        yield TextareaField::new('additional')->setColumns('col-md-8')->hideOnIndex();
        yield TextField::new('payment')->setColumns('col-md-8');
        /*yield ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public_html/uploads')
            ->setFormType(FileUploadType::class)
            ->setRequired(false);*/

        yield FormField::addPanel('Additional info')->setIcon('fa fa-info-circle')->setCssClass('col-sm-4');
        //yield FormField::addRow();
        yield AssociationField::new('city')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('district')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('category')->hideOnIndex()->setColumns('col-md-12');
        yield AssociationField::new('user')->setColumns('col-md-12')->setLabel('Profile owner')->hideOnIndex();

        yield FormField::addPanel('General requirements')->setIcon('fa fa-gear');
        yield FormField::addRow();
        yield TextField::new('age')->setColumns('col-md-4')->hideOnIndex();
        yield AssociationField::new('experience')->setColumns('col-md-4')->hideOnIndex();
        yield AssociationField::new('education')->setColumns('col-md-4')->hideOnIndex();
        yield FormField::addRow();
        yield AssociationField::new('citizen')->setColumns('col-md-4')->hideOnIndex();
        yield BooleanField::new('startNow')->hideOnIndex();
        yield DateField::new('startDate')->hideOnIndex();
    }
}
